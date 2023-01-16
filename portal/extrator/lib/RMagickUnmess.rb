module Magick

  class Image

    def unmess_columns(image)
      image.each_pixel do |pixel, x, y|
        if (image.pixel_color(x-1, y).red > 10000 and (image.pixel_color(x+1, y).red > 10000 or image.pixel_color(x+2, y).red > 10000))
          image.pixel_color(x, y, 'white')
        end
      end
    end

    def unmess_rows(image)
      image.each_pixel do |pixel, x, y|
        if (image.pixel_color(x, y-1).red > 10000 and (image.pixel_color(x, y+1).red > 10000 or image.pixel_color(x, y+2).red > 10000))
          image.pixel_color(x, y, 'white')
        end
      end
    end

    def chomp_borders(image)
      (0..image.columns).each do |x|
        (0..8).each do |y|
          image.pixel_color(x, y, 'white')
        end
        (image.rows-8..image.rows).each do |y|
          image.pixel_color(x, y, 'white')
        end
      end
    end

    def unmess(image)
      image_c = image.clone
      unmess_columns(image_c)
      unmess_rows(image_c)
      chomp_borders(image_c)
      image_c
    end

  end

end