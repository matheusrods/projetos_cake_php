require 'open-uri'
require 'watir'
require 'selenium-webdriver'
require 'nokogiri'
require 'json'

class ExtratorBase
  
  def browser()
    if not defined? @@browser or not @@browser
      client = Selenium::WebDriver::Remote::Http::Default.new
      client.timeout = 10
      @@browser = Watir::Browser.new :firefox, :http_client => client
    end
    @@browser
  end
  
  def navigate(url)
    browser().goto url
    self
  end
  
  def fill(data_map)
    data_map.each do |key, value|
      browser().text_field(:name => key.to_s).set value
    end
    self
  end
  
  def radio_set(value)
    browser().radio(:value => value).set
    self
  end
  
  def click(name)
    browser().button(:name => name).click
    self
  end
  
  def post(form_action)
    browser().form(:action, form_action).submit
    self
  end
  
  def close()
    browser().close
    @@browser = nil
  end
  
  def url
    browser().url
  end
  
  def parse()
    Nokogiri::HTML.parse(browser().html)
  end
  
  def random_chars(count)
    dict = [('0'..'9'),('A'..'Z')].map{|i| i.to_a}.flatten
    (0...4).map{ dict[rand(dict.length)] }.join
  end
  
  def break_captcha(wave_filename)
    WaveRecognizer.new(wave_filename).recognize_using(CHAR_SAMPLE_MAP)
  end
  
  def save(name, link)
    File.open(name, 'w') do |file|
      file.write open(link, :ssl_verify_mode => OpenSSL::SSL::VERIFY_NONE).read
    end
  end
  
end
