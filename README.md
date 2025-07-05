# 🖼️ Image Convert

> A powerful PHP package for converting images to WebP format and resizing them with optional object storage support.

[![Latest Version](https://img.shields.io/packagist/v/vishal-pawar/image-convert)](https://packagist.org/packages/vishal-pawar/image-convert)
[![PHP Version](https://img.shields.io/packagist/php-v/vishal-pawar/image-convert)](https://packagist.org/packages/vishal-pawar/image-convert)
[![License](https://img.shields.io/packagist/l/vishal-pawar/image-convert)](https://packagist.org/packages/vishal-pawar/image-convert)
[![Total Downloads](https://img.shields.io/packagist/dt/vishal-pawar/image-convert)](https://packagist.org/packages/vishal-pawar/image-convert)

## ✨ Features

- 🚀 **Fast WebP Conversion** - Convert JPG/PNG images to WebP format
- 📐 **Smart Resizing** - Resize images to desired dimensions
- ☁️ **Object Storage Support** - Save directly to cloud storage (DigitalOcean Spaces, AWS S3)
- 🎯 **Simple API** - Easy-to-use helper functions
- 🔧 **Laravel Integration** - Seamless integration with Laravel applications

## 📦 Installation

Install the package via Composer:

```bash
composer require vishal-pawar/image-convert:v1.0.0
```

## 🚀 Quick Start

```php
use VishalPawar\ImageConvert\helper\ImageHelper;

// Convert and save as WebP
$result = ImageHelper::saveWebpImage("uploads/images", $request->image);

// Resize and convert to WebP
$result = ImageHelper::resizeSaveWebpImage("uploads/images", $request->image, 800, 600);
```

## 📚 API Reference

### Available Methods

| Method | Description |
|--------|-------------|
| `saveImage()` | Save image in original format |
| `resizeSaveImage()` | Resize and save image |
| `saveWebpImage()` | Convert to WebP and save |
| `resizeSaveWebpImage()` | Resize, convert to WebP and save |

### Method Details

#### `saveImage(path, image, objectStore?)`

Save an image in its original format.

**Parameters:**
- `path` (string) - Directory path (e.g., 'uploads/images')
- `image` (file) - Image file object
- `objectStore` (boolean, optional) - Enable object storage (default: false)

**Example:**
```php
Route::post('upload', function(Request $request) {
    return ImageHelper::saveImage("uploads/posts", $request->image);
});
```

#### `resizeSaveImage(path, image, height, width, objectStore?)`

Resize and save an image.

**Parameters:**
- `path` (string) - Directory path
- `image` (file) - Image file object
- `height` (int) - Target height in pixels
- `width` (int) - Target width in pixels
- `objectStore` (boolean, optional) - Enable object storage (default: false)

**Example:**
```php
Route::post('upload', function(Request $request) {
    return ImageHelper::resizeSaveImage("uploads/posts", $request->image, 320, 320);
});
```

#### `saveWebpImage(path, image, objectStore?)`

Convert image to WebP format and save.

**Parameters:**
- `path` (string) - Directory path
- `image` (file) - Image file object
- `objectStore` (boolean, optional) - Enable object storage (default: false)

**Example:**
```php
Route::post('upload', function(Request $request) {
    return ImageHelper::saveWebpImage("uploads/posts", $request->image);
});
```

#### `resizeSaveWebpImage(path, image, height, width, objectStore?)`

Resize image and convert to WebP format.

**Parameters:**
- `path` (string) - Directory path
- `image` (file) - Image file object
- `height` (int) - Target height in pixels
- `width` (int) - Target width in pixels
- `objectStore` (boolean, optional) - Enable object storage (default: false)

**Example:**
```php
Route::post('upload', function(Request $request) {
    return ImageHelper::resizeSaveWebpImage("uploads/posts", $request->image, 800, 600);
});
```

## ☁️ Object Storage Configuration

To enable object storage functionality, create a configuration file:

### Step 1: Create Config File

Create `app/config/ImageConvert.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Object Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your object storage settings for cloud storage providers
    | like DigitalOcean Spaces, AWS S3, etc.
    |
    */
    'do_spaces' => [
        'driver'         => 's3',
        'key'            => env('DO_KEY'),
        'secret'         => env('DO_SECRET'),
        'region'         => env('DO_REGION'),
        'bucket'         => env('DO_BUCKET'),
        'endpoint'       => env('DO_ENDPOINT'),
        'originendpoint' => env('ORIGIN_ENDPOINT'), // Full endpoint URL
    ],
];
```

### Step 2: Environment Variables

Add these variables to your `.env` file:

```env
DO_KEY=your_digitalocean_key
DO_SECRET=your_digitalocean_secret
DO_REGION=your_region
DO_BUCKET=your_bucket_name
DO_ENDPOINT=https://region.digitaloceanspaces.com
ORIGIN_ENDPOINT=https://your-bucket.region.digitaloceanspaces.com
```

### Step 3: Enable Object Storage

Pass `true` or `1` as the last parameter to enable object storage:

```php
// Save to object storage
ImageHelper::saveWebpImage("uploads/posts", $request->image, true);

// Resize and save to object storage
ImageHelper::resizeSaveWebpImage("uploads/posts", $request->image, 800, 600, true);
```

## 💡 Usage Examples

### Basic Image Upload

```php
use VishalPawar\ImageConvert\helper\ImageHelper;

Route::post('image-upload', function(Request $request) {
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);
    
    $result = ImageHelper::saveWebpImage("uploads/posts", $request->image);
    
    return response()->json([
        'success' => true,
        'path' => $result
    ]);
});
```

### Thumbnail Generation

```php
Route::post('upload-thumbnail', function(Request $request) {
    // Create multiple sizes
    $thumbnail = ImageHelper::resizeSaveWebpImage("uploads/thumbnails", $request->image, 150, 150);
    $medium = ImageHelper::resizeSaveWebpImage("uploads/medium", $request->image, 500, 500);
    $large = ImageHelper::resizeSaveWebpImage("uploads/large", $request->image, 1200, 1200);
    
    return response()->json([
        'thumbnail' => $thumbnail,
        'medium' => $medium,
        'large' => $large
    ]);
});
```

## 🔧 Requirements

- PHP >= 8.1
- Laravel >= 10.10
- GD or Imagick extension

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🐛 Issues

If you discover any issues, please create an issue on the [GitHub repository](https://github.com/vishal-pawar/image-convert).

## 📧 Support

For support, email [vishalpratapsinghpawar7@gmail.com](mailto:vishalpratapsinghpawar7@gmail.com) or create an issue on GitHub.

---

<div align="center">
    <strong>Made with ❤️ by Vishal Pawar</strong>
</div>
