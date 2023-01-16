# rackup -E production

require 'rubygems'
require 'logger'
require 'sinatra/base'
require '../lib/ExtratorBase.rb'
require '../lib/MessCaptchaRecognizer.rb'

if ENV["RACK_ENV"] == 'production'
  puts "using headless"
  require 'headless'
  headless = Headless.new
  headless.start
end

class ExtratorDenatranBase < ExtratorBase

  def initialize
    @logger = Logger.new(STDOUT)
  end

  def auto_retry(base_url, fill_map, post_to)
    max_retries = 3
    home = nil
    while home == nil and max_retries > 0 do
      begin
        home = navigate base_url
        while home.url.start_with? base_url and max_retries > 0 do
          link_image_captcha = 'https://denatran.serpro.gov.br/' + home.parse().css('#imgcaptcha').first.attributes['src'].value
          save("_captcha.png", link_image_captcha)
          @logger.info "Captcha salvo"
          fill_map[:captcha] = MessCaptchaRecognizer.new().recognize("_captcha.png")
          @logger.info "Captcha identificado: #{fill_map[:captcha]}"
          home.fill(fill_map).post(post_to)
          # home.browser.div(:class => 'div-print').wait_until_present
        end
      rescue Timeout::Error, Watir::Wait::TimeoutError
        @logger.warn "Timeout error"
	home.close if home != nil
        home = nil
      end
    end
    yield home
  end
  
end

class ExtratorDenatranCnh < ExtratorDenatranBase
  
  def run(cpf, registro, seguranca)
    home = nil
    base_url = 'https://denatran.serpro.gov.br/Numero_Seguranca_CNH.asp'
    fill_map = {txtCPF: cpf, txtNroRegistro: registro, txtNroSeguranca:seguranca}
    post_to = 'carteira_Assinatura.asp'
    auto_retry base_url, fill_map, post_to do |home|
      page = home.parse
      response_data = { }
      page.css('input').each do |input|
        response_data[input.attributes['id'].value.strip] = input.attributes['value'].value.strip
      end
      response_data.to_json
    end
  end
  
end

class ExtratorDenatranVeiculo < ExtratorDenatranBase
  
  def run(cpf, renavam)
    base_url = 'https://denatran.serpro.gov.br/Veiculo_Consulta.asp'
    fill_map = {txtCPFCNPJ: cpf, txtCodRenavam: renavam}
    post_to = 'Veiculo_Consulta_Result.asp'
    auto_retry base_url, fill_map, post_to do |home|
      page = home.parse
      response_data = { }
      page.css('table tr').each do |row|
        if row.css('td')[0].attributes['class'] and row.css('td')[0].attributes['class'].value == 'filtro'
          response_data[row.css('td')[0].content.strip] = row.css('td')[1].content.strip
        end
      end
      response_data.to_json
    end
  end

end

class ExtratorApp < Sinatra::Base

  get '/denatran/cnh/:cpf/:registro/:seguranca' do
    ExtratorDenatranCnh.new().run(params[:cpf], params[:registro], params[:seguranca])
  end
  
  get '/denatran/veiculo/:cpf/:renavam' do
    ExtratorDenatranVeiculo.new().run(params[:cpf], params[:renavam])
  end
  
end
