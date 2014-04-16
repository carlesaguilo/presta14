<?php



/**

 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />

 * File:        securimage_show_example.php<br />

 *

 * This library is free software; you can redistribute it and/or

 * modify it under the terms of the GNU Lesser General Public

 * License as published by the Free Software Foundation; either

 * version 2.1 of the License, or any later version.<br /><br />

 *

 * This library is distributed in the hope that it will be useful,

 * but WITHOUT ANY WARRANTY; without even the implied warranty of

 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU

 * Lesser General Public License for more details.<br /><br />

 *

 * You should have received a copy of the GNU Lesser General Public

 * License along with this library; if not, write to the Free Software

 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA<br /><br />

 *

 * Any modifications to the library should be indicated clearly in the source code

 * to inform users that the changes are not a part of the original software.<br /><br />

 *

 * If you found this script useful, please take a quick moment to rate it.<br />

 * http://www.hotscripts.com/rate/49400.html  Thanks.

 *

 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA

 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version

 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation

 * @copyright 2009 Drew Phillips

 * @author Drew Phillips <drew@drew-phillips.com>

 * @version 2.0.1 BETA (December 6th, 2009)

 * @package Securimage

 *

 */



include 'securimage.php';

$line=intval($_GET['line']);

$image_width=(isset($_GET['width'])?$_GET['width']:250);

$image_height=(isset($_GET['height'])?$_GET['height']:80);

$angle=(isset($_GET['angle'])?$_GET['angle']:5);

$opacity=(isset($_GET['opacity'])?$_GET['opacity']:10);

$copy=(isset($_GET['copy'])?$_GET['copy']:'aretmic');

$bg=(isset($_GET['bg'])?$_GET['bg']:'bg3.jpg');

$font=(isset($_GET['font'])?$_GET['font']:'times.ttf');

$noise=(isset($_GET['noise'])?$_GET['noise']:0.50);

$useword=(isset($_GET['useword'])?$_GET['useword']:'true');



$img = new securimage();



//Change some settings

$img->code_length = 6;

$img->gd_font_size  = 24;

$img->image_width = $image_width;

$img->image_height = $image_height;

$img->perturbation = $noise;

$img->image_bg_color = new Securimage_Color("#f6f6f6");

$img->multi_text_color = array(new Securimage_Color("#3399ff"),

                               new Securimage_Color("#3300cc"),

                               new Securimage_Color("#3333cc"),

                               new Securimage_Color("#6666ff"),

                               new Securimage_Color("#99cccc")

                               );

$img->use_multi_text = true;

$img->text_angle_minimum = -$angle;

$img->text_angle_maximum = $angle;

$img->use_transparent_text = true;

$img->text_transparency_percentage = $opacity; // 100 = completely transparent

$img->num_lines = $line;

$img->line_color = new Securimage_Color("#eaeaea");

$img->image_signature = $copy;

$img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255));

$img->use_wordlist = $useword;

$img->ttf_file='fonts/'.$font;



$img->show('backgrounds/'.$bg); // alternate use:  $img->show('/path/to/background_image.jpg');



