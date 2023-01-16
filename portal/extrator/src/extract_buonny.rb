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

class Watir::Element

  # If any parent element isn't visible then we cannot write to the
  # element. The only realiable way to determine this is to iterate
  # up the DOM element tree checking every element to make sure it's
  # visible.
  def visible?
    # Now iterate up the DOM element tree and return false if any
    # parent element isn't visible or is disabled.
    object = document
    while object
      begin
        if object.style.invoke('visibility') =~ /^hidden$/i
          return false
        end
        if object.style.invoke('display') =~ /^none$/i
          return false
        end
        if object.invoke('isDisabled')
          return false
        end
      rescue WIN32OLERuntimeError
      end
      object = object.parentElement
    end
    true
  end
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
          #home.browser.div(:class => 'div-print').wait_until_present
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

class ExtratorStj < ExtratorBase
  
  def run(nome)
    base_url = 'http://www.stj.jus.br/webstj/processo/justica/Default.asp'
    fill_map = {nom_par: nome}
    home = navigate base_url
    home.click('bt_pesquisa_avancada') unless home.browser().radio(:value => '=').visible?
    home.fill(fill_map).radio_set('=').post('valida.asp')
    # home.browser.div(:class => 'div-print').wait_until_present
    page = home.parse
    partes_encontradas = page.css('input[type=checkbox][name=key2]').count
    {partes_encontradas:partes_encontradas}.to_json
  end
  
end

class ExtratorApp < Sinatra::Base

  get '/denatran/cnh/:cpf/:registro/:seguranca' do
    ExtratorDenatranCnh.new().run(params[:cpf], params[:registro], params[:seguranca])
  end
  
  get '/denatran/veiculo/:cpf/:renavam' do
    ExtratorDenatranVeiculo.new().run(params[:cpf], params[:renavam])
  end
  
  get '/stj/:nome' do
    ExtratorStj.new().run(params[:nome])
  end
  
end

