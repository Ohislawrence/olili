<?php
// app/Services/CertificateImageService.php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateImageService
{
    /**
     * Create certificate image using pure GD
     */
    public function createCertificateImage(array $data, string $backgroundColor = '#ffffff', string $textColor = '#000000'): string
    {
        // Check if GD is available
        if (!extension_loaded('gd')) {
            throw new \Exception('GD extension is not available');
        }

        $width = 1200;
        $height = 800;

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Convert hex color to RGB
        $bgColor = $this->hexToRgb($backgroundColor, $image);
        $txtColor = $this->hexToRgb($textColor, $image);

        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        // Add decorative border
        $borderColor = imagecolorallocate($image, 200, 200, 200);
        imagerectangle($image, 10, 10, $width - 10, $height - 10, $borderColor);
        imagerectangle($image, 12, 12, $width - 12, $height - 12, $borderColor);

        // Check if TrueType fonts are available
        $ttfFontPath = $this->getFontPath();
        $hasTtfFont = !empty($ttfFontPath) && file_exists($ttfFontPath);

        // Add header
        $this->addHeader($image, $data, $width, $hasTtfFont, $txtColor, $ttfFontPath);

        // Add main content
        $this->addMainContent($image, $data, $width, $height, $hasTtfFont, $txtColor, $ttfFontPath);

        // Add footer
        $this->addFooter($image, $data, $width, $height, $hasTtfFont, $txtColor, $ttfFontPath);

        // Add logo if available
        if (!empty($data['issuer']['logo'])) {
            $this->addLogoToImage($image, $data['issuer']['logo'], $width);
        }

        // Add QR code if available
        if (!empty($data['qr_code'])) {
            $this->addQrCodeToImage($image, $data['qr_code'], $width, $height);
        }

        // Save image
        $fileName = "certificates/images/{$data['certificate_number']}.png";
        $filePath = storage_path("app/public/{$fileName}");

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('certificates/images');

        // Save the image
        imagepng($image, $filePath);
        imagedestroy($image);

        return Storage::url($fileName);
    }

    /**
     * Convert hex color to GD color
     */
    private function hexToRgb(string $hex, $image)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return imagecolorallocate($image, $r, $g, $b);
    }

    /**
     * Get font path
     */
    private function getFontPath(): string
    {
        // Check for common font locations
        $possiblePaths = [
            public_path('fonts/Roboto-Regular.ttf'),
            public_path('fonts/arial.ttf'),
            public_path('fonts/DejaVuSans.ttf'),
            storage_path('fonts/Roboto-Regular.ttf'),
            base_path('resources/fonts/Roboto-Regular.ttf'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Add header section
     */
    private function addHeader($image, array $data, int $width, bool $hasTtfFont, int $txtColor, string $ttfFontPath): void
    {
        $yPosition = 80;

        if ($hasTtfFont) {
            // Certificate title
            $title = "Certificate of Completion";
            $titleSize = 36;
            $titleBox = imagettfbbox($titleSize, 0, $ttfFontPath, $title);
            $titleWidth = $titleBox[2] - $titleBox[0];
            $titleX = ($width - $titleWidth) / 2;
            imagettftext($image, $titleSize, 0, $titleX, $yPosition, $txtColor, $ttfFontPath, $title);

            // Issuer name
            $issuer = $data['issuer']['name'] ?? 'Olilearn AI Learning Platform';
            $issuerSize = 18;
            $issuerBox = imagettfbbox($issuerSize, 0, $ttfFontPath, $issuer);
            $issuerWidth = $issuerBox[2] - $issuerBox[0];
            $issuerX = ($width - $issuerWidth) / 2;
            imagettftext($image, $issuerSize, 0, $issuerX, $yPosition + 50, $txtColor, $ttfFontPath, $issuer);
        } else {
            // Using GD built-in fonts
            $title = "Certificate of Completion";
            $titleWidth = imagefontwidth(5) * strlen($title);
            $titleX = ($width - $titleWidth) / 2;
            imagestring($image, 5, $titleX, $yPosition - 20, $title, $txtColor);

            $issuer = $data['issuer']['name'] ?? 'Olilearn AI Learning Platform';
            $issuerWidth = imagefontwidth(3) * strlen($issuer);
            $issuerX = ($width - $issuerWidth) / 2;
            imagestring($image, 3, $issuerX, $yPosition + 30, $issuer, $txtColor);
        }

        // Add decorative line
        $lineY = $yPosition + 80;
        $lineColor = imagecolorallocate($image, 102, 126, 234); // Blue color
        imageline($image, 100, $lineY, $width - 100, $lineY, $lineColor);
        imageline($image, 100, $lineY + 1, $width - 100, $lineY + 1, $lineColor);
    }

    /**
     * Add main content
     */
    private function addMainContent($image, array $data, int $width, int $height, bool $hasTtfFont, int $txtColor, string $ttfFontPath): void
    {
        $centerY = $height / 2;

        if ($hasTtfFont) {
            // "This is to certify that"
            $text1 = "This is to certify that";
            $size1 = 20;
            $box1 = imagettfbbox($size1, 0, $ttfFontPath, $text1);
            $width1 = $box1[2] - $box1[0];
            $x1 = ($width - $width1) / 2;
            imagettftext($image, $size1, 0, $x1, $centerY - 60, $txtColor, $ttfFontPath, $text1);

            // Student name
            $studentName = $data['student_name'];
            $size2 = 48;
            $box2 = imagettfbbox($size2, 0, $ttfFontPath, $studentName);
            $width2 = $box2[2] - $box2[0];
            $x2 = ($width - $width2) / 2;
            imagettftext($image, $size2, 0, $x2, $centerY, $txtColor, $ttfFontPath, $studentName);

            // "has successfully completed"
            $text3 = "has successfully completed";
            $size3 = 20;
            $box3 = imagettfbbox($size3, 0, $ttfFontPath, $text3);
            $width3 = $box3[2] - $box3[0];
            $x3 = ($width - $width3) / 2;
            imagettftext($image, $size3, 0, $x3, $centerY + 60, $txtColor, $ttfFontPath, $text3);

            // Course title
            $courseTitle = $data['course_title'];
            $size4 = 28;
            $box4 = imagettfbbox($size4, 0, $ttfFontPath, $courseTitle);
            $width4 = $box4[2] - $box4[0];
            $x4 = ($width - $width4) / 2;
            imagettftext($image, $size4, 0, $x4, $centerY + 100, $txtColor, $ttfFontPath, $courseTitle);
        } else {
            // Using GD built-in fonts
            $text1 = "This is to certify that";
            $width1 = imagefontwidth(4) * strlen($text1);
            $x1 = ($width - $width1) / 2;
            imagestring($image, 4, $x1, $centerY - 80, $text1, $txtColor);

            $studentName = $data['student_name'];
            $width2 = imagefontwidth(5) * strlen($studentName);
            $x2 = ($width - $width2) / 2;
            imagestring($image, 5, $x2, $centerY - 40, $studentName, $txtColor);

            $text3 = "has successfully completed";
            $width3 = imagefontwidth(4) * strlen($text3);
            $x3 = ($width - $width3) / 2;
            imagestring($image, 4, $x3, $centerY + 20, $text3, $txtColor);

            $courseTitle = $data['course_title'];
            $width4 = imagefontwidth(4) * strlen($courseTitle);
            $x4 = ($width - $width4) / 2;
            imagestring($image, 4, $x4, $centerY + 60, $courseTitle, $txtColor);
        }
    }

    /**
     * Add footer section
     */
    private function addFooter($image, array $data, int $width, int $height, bool $hasTtfFont, int $txtColor, string $ttfFontPath): void
    {
        $footerY = $height - 100;

        if ($hasTtfFont) {
            // Issue date
            $issueDate = "Issued on: " . ($data['issue_date'] ?? date('F j, Y'));
            $size = 16;
            imagettftext($image, $size, 0, 100, $footerY, $txtColor, $ttfFontPath, $issueDate);

            // Certificate number
            $certNumber = "Certificate #: " . ($data['certificate_number'] ?? 'N/A');
            $box = imagettfbbox($size, 0, $ttfFontPath, $certNumber);
            $certWidth = $box[2] - $box[0];
            $certX = $width - $certWidth - 100;
            imagettftext($image, $size, 0, $certX, $footerY, $txtColor, $ttfFontPath, $certNumber);

            // Expiry date if exists
            if (!empty($data['expiry_date'])) {
                $expiryDate = "Valid until: " . $data['expiry_date'];
                $expiryBox = imagettfbbox($size, 0, $ttfFontPath, $expiryDate);
                $expiryWidth = $expiryBox[2] - $expiryBox[0];
                $expiryX = ($width - $expiryWidth) / 2;
                imagettftext($image, $size, 0, $expiryX, $footerY + 30, $txtColor, $ttfFontPath, $expiryDate);
            }
        } else {
            // Using GD built-in fonts
            $issueDate = "Issued on: " . ($data['issue_date'] ?? date('F j, Y'));
            imagestring($image, 3, 100, $footerY - 20, $issueDate, $txtColor);

            $certNumber = "Certificate #: " . ($data['certificate_number'] ?? 'N/A');
            $certWidth = imagefontwidth(3) * strlen($certNumber);
            $certX = $width - $certWidth - 100;
            imagestring($image, 3, $certX, $footerY - 20, $certNumber, $txtColor);

            if (!empty($data['expiry_date'])) {
                $expiryDate = "Valid until: " . $data['expiry_date'];
                $expiryWidth = imagefontwidth(3) * strlen($expiryDate);
                $expiryX = ($width - $expiryWidth) / 2;
                imagestring($image, 3, $expiryX, $footerY + 10, $expiryDate, $txtColor);
            }
        }

        // Add bottom decorative line
        $bottomLineY = $height - 120;
        $lineColor = imagecolorallocate($image, 102, 126, 234);
        imageline($image, 100, $bottomLineY, $width - 100, $bottomLineY, $lineColor);
        imageline($image, 100, $bottomLineY + 1, $width - 100, $bottomLineY + 1, $lineColor);
    }

    /**
     * Add logo to image
     */
    private function addLogoToImage($image, string $logoPath, int $width): void
    {
        try {
            // Try different path formats
            $possiblePaths = [
                public_path($logoPath),
                storage_path('app/public/' . str_replace('/storage/', '', $logoPath)),
                $logoPath, // Absolute path
            ];

            $logo = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    if (str_ends_with(strtolower($path), '.png')) {
                        $logo = imagecreatefrompng($path);
                    } elseif (str_ends_with(strtolower($path), '.jpg') || str_ends_with(strtolower($path), '.jpeg')) {
                        $logo = imagecreatefromjpeg($path);
                    } elseif (str_ends_with(strtolower($path), '.gif')) {
                        $logo = imagecreatefromgif($path);
                    }

                    if ($logo) {
                        break;
                    }
                }
            }

            if ($logo) {
                $logoWidth = imagesx($logo);
                $logoHeight = imagesy($logo);

                // Resize if too large
                $maxWidth = 200;
                if ($logoWidth > $maxWidth) {
                    $ratio = $maxWidth / $logoWidth;
                    $newWidth = $maxWidth;
                    $newHeight = $logoHeight * $ratio;

                    $resizedLogo = imagecreatetruecolor($newWidth, $newHeight);

                    // Preserve transparency for PNG
                    imagealphablending($resizedLogo, false);
                    imagesavealpha($resizedLogo, true);
                    $transparent = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
                    imagefill($resizedLogo, 0, 0, $transparent);

                    imagecopyresampled($resizedLogo, $logo, 0, 0, 0, 0, $newWidth, $newHeight, $logoWidth, $logoHeight);

                    imagedestroy($logo);
                    $logo = $resizedLogo;
                    $logoWidth = $newWidth;
                    $logoHeight = $newHeight;
                }

                // Position at top center
                $x = ($width - $logoWidth) / 2;
                $y = 20;

                imagecopy($image, $logo, $x, $y, 0, 0, $logoWidth, $logoHeight);
                imagedestroy($logo);
            }
        } catch (\Exception $e) {
            Log::error('Failed to add logo to certificate: ' . $e->getMessage());
        }
    }

    /**
     * Add QR code to image
     */
    private function addQrCodeToImage($image, string $qrCodeUrl, int $width, int $height): void
    {
        try {
            $qrPath = storage_path('app/public/' . str_replace('/storage/', '', $qrCodeUrl));

            if (file_exists($qrPath)) {
                $qr = imagecreatefrompng($qrPath);
                if ($qr) {
                    $qrWidth = imagesx($qr);
                    $qrHeight = imagesy($qr);

                    // Resize QR code
                    $size = 100;
                    $resizedQr = imagecreatetruecolor($size, $size);

                    imagecopyresampled($resizedQr, $qr, 0, 0, 0, 0, $size, $size, $qrWidth, $qrHeight);

                    // Position at bottom right
                    $x = $width - $size - 50;
                    $y = $height - $size - 50;

                    imagecopy($image, $resizedQr, $x, $y, 0, 0, $size, $size);

                    imagedestroy($qr);
                    imagedestroy($resizedQr);

                    // Add "Scan to verify" text
                    $textColor = imagecolorallocate($image, 100, 100, 100);
                    $text = "Scan to verify";
                    imagestring($image, 2, $x + 10, $y - 15, $text, $textColor);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to add QR code to certificate: ' . $e->getMessage());
        }
    }

    /**
     * Check if GD extension is available
     */
    public function isGdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    /**
     * Get GD version information
     */
    public function getGdInfo(): array
    {
        if (!$this->isGdAvailable()) {
            return ['available' => false];
        }

        return [
            'available' => true,
            'version' => gd_info(),
            'freetype' => function_exists('imagettftext'),
            'supported_formats' => [
                'jpeg' => function_exists('imagecreatefromjpeg'),
                'png' => function_exists('imagecreatefrompng'),
                'gif' => function_exists('imagecreatefromgif'),
                'webp' => function_exists('imagecreatefromwebp'),
            ],
        ];
    }
}
