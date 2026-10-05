<?php
// // -------- SETTINGS --------
// $version = 'v1'; // change this when you tweak size/position
// $watermarkPath = __DIR__ . '/storage/assets/logo/logo.png'; // your PNG
// $allowed1 = 'storage/assets/memberPhotos/';
// $allowed2 = 'storage/assets/memberBlurPhotos/';
// // --------------------------

// $reqPath = $_GET['path'] ?? '';

// // Allow only two folders
// if (
//     strpos($reqPath, $allowed1) !== 0 &&
//     strpos($reqPath, $allowed2) !== 0
// ) {
//     http_response_code(403);
//     exit;
// }

// $originalPath = __DIR__ . '/' . $reqPath;
// $cachePath = __DIR__ . '/watermark_cache/' . md5($version.$reqPath) . '.webp';

// if (!file_exists($originalPath) || !file_exists($watermarkPath)) {
//     http_response_code(404);
//     exit;
// }

// // Serve from cache (static speed)
// if (file_exists($cachePath)) {
//     header('Content-Type: image/webp');
//     readfile($cachePath);
//     exit;
// }

// // -------- Load main image --------
// $ext = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));

// switch ($ext) {
//     case 'jpg':
//     case 'jpeg':
//         $main = imagecreatefromjpeg($originalPath);
//         break;
//     case 'png':
//         $main = imagecreatefrompng($originalPath);
//         break;
//     case 'webp':
//         $main = imagecreatefromwebp($originalPath);
//         break;
//     default:
//         http_response_code(415);
//         exit;
// }

// if (!$main) {
//     http_response_code(500);
//     exit;
// }

// // -------- Load watermark PNG --------
// $wm = imagecreatefrompng($watermarkPath);

// if (!$wm) {
//     die('Invalid watermark PNG');
// }

// // Preserve alpha
// imagealphablending($wm, true);
// imagesavealpha($wm, true);

// // -------- Main image size --------
// $mw = imagesx($main);
// $mh = imagesy($main);

// // -------- Watermark original size --------
// $ww = imagesx($wm);
// $wh = imagesy($wm);

// // =====================================
// // Resize watermark PROPERLY
// // =====================================

// // Make watermark height 70% of image height
// $newH = $mh * 0.70;

// // Auto width ratio
// $newW = ($ww / $wh) * $newH;

// // Create transparent canvas
// $resizedWM = imagecreatetruecolor($newW, $newH);

// imagealphablending($resizedWM, false);
// imagesavealpha($resizedWM, true);

// $transparent = imagecolorallocatealpha($resizedWM, 0, 0, 0, 127);
// imagefilledrectangle($resizedWM, 0, 0, $newW, $newH, $transparent);

// // Resize smoothly
// imagecopyresampled(
//     $resizedWM,
//     $wm,
//     0,
//     0,
//     0,
//     0,
//     $newW,
//     $newH,
//     $ww,
//     $wh
// );

// // ======================================
// // Position RIGHT + BOTTOM
// $margin = 0;

// $x = $mw - $newW - $margin;
// $y = $mh - $newH - $margin;

// // =====================================
// // Merge watermark
// // =====================================

// imagealphablending($main, true);

// imagecopy(
//     $main,
//     $resizedWM,
//     $x,
//     $y,
//     0,
//     0,
//     $newW,
//     $newH
// );

// // -------- Cache folder --------
// if (!is_dir(dirname($cachePath))) {
//     mkdir(dirname($cachePath), 0777, true);
// }

// // -------- Save + Output --------
// imagewebp($main, $cachePath, 85);

// header('Content-Type: image/webp');
// imagewebp($main, null, 85);

// // -------- Cleanup --------
// imagedestroy($main);
// imagedestroy($wm);
// imagedestroy($resizedWM);