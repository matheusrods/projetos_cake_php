$LOAD_PATH << File.dirname(__FILE__)

require 'RMagick'
require '../lib/RMagickUnmess'

class MessCaptchaRecognizer

  def recognize(file_path)
    image = Magick::ImageList.new(file_path)
    image = image.unmess(image)
    tesseract(image, file_path)
  end

  def tesseract(image, file_path)
    image.write('_captcha-mod.png')
    `tesseract _captcha-mod.png out 2> /dev/null`
    resp_o = `cat out.txt`.strip.upcase
    resp_o.gsub(/[^A-Z0-9]/, '')
  end
end
