<?php   
  
  define( '_JEXEC', 1 );
  define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));

  require_once ( JPATH_BASE. '/includes/defines.php' );
  require_once ( JPATH_BASE. '/includes/framework.php' );
  $mainframe =JFactory::getApplication('site');
  $mainframe->initialise();

  require_once JPATH_BASE.'/components/com_multipolls/captcha/generate.php';
  $crypt_captcha = $_GET['data'];
  $str_captcha = encrypt_decrypt('decrypt' , $crypt_captcha ); 
  
  $width = 120; $height = 40; //ширина и высота картинки
  $font = 'comic.ttf';//шрифт текста
  $fontsize = 14;// размер текста

  header('Content-type: image/png'); //тип возвращаемого содержимого (картинка в формате PNG) 

  $im = imagecreatetruecolor($width, $height); //создаёт новое изображение
  imagesavealpha($im, true); //устанавливает прозрачность изображения
  $bg = imagecolorallocatealpha($im, 0, 0, 0, 127); //идентификатор цвета для изображения
  imagefill($im, 0, 0, $bg); //выполняет заливку цветом
  
  putenv( 'GDFONTPATH=' . realpath('.') ); //проверяет путь до файла со шрифтами

  $captcha = '';

  for ($i = 0; $i < strlen($str_captcha); $i++){
    $captcha .= $str_captcha[$i];
    $x = ($width - 20) / strlen($str_captcha) * $i + 10;//растояние между символами
    $x = rand($x, $x+4);//случайное смещение
    $y = $height - ( ($height - $fontsize) / 2 ); // координата Y
    $curcolor = imagecolorallocate( $im, rand(0, 100), rand(0, 100), rand(0, 100) );//цвет для текущей буквы
    $angle = rand(-25, 25);//случайный угол наклона 
    imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]); //вывод текста

    $captcha = mb_strtolower($captcha);
  }
 
  imagepng($im); //выводим изображение    
  
  imagedestroy($im);//очищаем память

?>