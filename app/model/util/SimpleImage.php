<?php
/**
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Modified by: Miguel Fermín
 * Based in: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 */

namespace app\model\util;

use app\model\service\exception\MyException;

class SimpleImage {

    private $image_info;
    public $image;

    /**
     * Konstruktor třídy SimpleImage
     * @param string|null $filename Cesta k souboru s obrázkem
     * @throws MyException Pokud obrázek nelze načíst
     */
    public function __construct ($filename = null) {
        if (!empty($filename)) {
            $this->load($filename);
        }
    }

    /**
     * Načte obrázek
     *
     * @param $filename string Cesta k souboru s obrázkem
     * @throws MyException Pokud obrázek nelze načíst
     */
    public function load ($filename) {
        $image_info = getimagesize($filename);
        $this->image_info = $image_info;

        if ($this->image_info[2] == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_info[2] == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_info[2] == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        } else {
            throw new MyException("Soubor, který se pokoušíte otevřít, není podporovaný");
        }
    }

    /**
     * Uloží obrázek
     *
     * @param $filename string Cesta k obrázku, kam má být uložen
     * @param int $image_type Typ obrázku, výhozí je JPEG
     * @param int $compression Komprese obrázku, výchozí je 75
     * @param null $permissions Oprávnění, výchozí je null
     */
    public function save ($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }

        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    public function output ($image_type = IMAGETYPE_JPEG, $quality = 80) {
        if ($image_type == IMAGETYPE_JPEG) {
            header("Content-type: image/jpeg");
            imagejpeg($this->image, null, $quality);
        } elseif ($image_type == IMAGETYPE_GIF) {
            header("Content-type: image/gif");
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            header("Content-type: image/png");
            imagepng($this->image);
        }
    }

    /**
     * Zjistí, zda-li je obrázek správného typu
     *
     * @param $extension integer Požadovaný typ obrázku
     * @return bool True, pokud je obrázek správného typu, jinak false
     */
    public function isValid($extension) {
        return $this->image_info[2] == $extension;

    }

    /**
     * @return int Šířka obrázku.
     */
    public function getWidth () {
        return imagesx($this->image);
    }

    /**
     * @return int Výška obrázku
     */
    public function getHeight () {
        return imagesy($this->image);
    }

    /**
     * Změní velikost podle výšky
     *
     * @param $height int Nová výška
     */
    public function resizeToHeight ($height) {
        $ratio = $height / $this->getHeight();
        $width = round($this->getWidth() * $ratio);
        $this->resize($width, $height);
    }

    /**
     * Změní velikost podle šířky
     *
     * @param $width int Nová šířka
     */
    public function resizeToWidth ($width) {
        $ratio = $width / $this->getWidth();
        $height = round($this->getHeight() * $ratio);
        $this->resize($width, $height);
    }

    /**
     * Změní obrázek na čtvercový
     *
     * @param $size int Velikost čtverce
     */
    public function square ($size) {
        $new_image = imagecreatetruecolor($size, $size);

        if ($this->getWidth() > $this->getHeight()) {
            $this->resizeToHeight($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);

        } else {
            $this->resizeToWidth($size);

            imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);

        }

        $this->image = $new_image;
    }

    /**
     * Naškáluje obrázek
     *
     * @param $scale int Velikost změny
     */
    public function scale ($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Změní velikost obrázku na zadanou
     *
     * @param $width int Nová šířka
     * @param $height int Nový výška
     */
    public function resize ($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    /**
     * Ořízne obrázek
     *
     * @param $x int X-ová souřadnice levého horního rohu
     * @param $y int Y-ová souřadnice levého horního rohu
     * @param $width int Šířka oříznutého obrázku
     * @param $height int Výška oříznutého obrázku
     * @return resource Reference na nový obrázek
     */
    public function cut ($x, $y, $width, $height) {
        $new_image = imagecreatetruecolor($width, $height);

        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);

        $this->image = $new_image;

        return $new_image;
    }

    public function maxarea ($width, $height = null) {
        $height = $height ? $height : $width;

        if ($this->getWidth() > $width) {
            $this->resizeToWidth($width);
        }
        if ($this->getHeight() > $height) {
            $this->resizeToheight($height);
        }
    }

    public function minarea ($width, $height = null) {
        $height = $height ? $height : $width;

        if ($this->getWidth() < $width) {
            $this->resizeToWidth($width);
        }
        if ($this->getHeight() < $height) {
            $this->resizeToheight($height);
        }
    }

    /**
     * Ořízne obrázek z prostředka
     *
     * @param $width int Šířka nového obrázku
     * @param $height int Výška nového obrázku
     * @return resource Reference na nový obrázek
     */
    public function cutFromCenter ($width, $height) {

        if ($width < $this->getWidth() && $width > $height) {
            $this->resizeToWidth($width);
        }
        if ($height < $this->getHeight() && $width < $height) {
            $this->resizeToHeight($height);
        }

        $x = ($this->getWidth() / 2) - ($width / 2);
        $y = ($this->getHeight() / 2) - ($height / 2);

        return $this->cut($x, $y, $width, $height);
    }

    public function maxareafill ($width, $height, $red = 0, $green = 0, $blue = 0) {
        $this->maxarea($width, $height);
        $new_image = imagecreatetruecolor($width, $height);
        $color_fill = imagecolorallocate($new_image, $red, $green, $blue);
        imagefill($new_image, 0, 0, $color_fill);
        imagecopyresampled($new_image, $this->image, floor(($width - $this->getWidth()) / 2), floor(($height - $this->getHeight()) / 2), 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

}