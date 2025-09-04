# Storage System Documentation

## Overview

The Comida SM project now uses a secure storage system outside the public directory for uploaded files. This provides better security and follows best practices for file management.

## Directory Structure

```
project-root/
├── public/                 # Web-accessible directory
│   ├── index.php          # Entry point
│   ├── css/               # Static CSS files
│   └── js/                # Static JS files
├── storage/               # Secure file storage (not web-accessible)
│   ├── logos/             # Establishment logos
│   ├── products/          # Product images
│   ├── categories/        # Category images
│   └── general/           # General uploads
└── src/
    └── Controllers/
        └── FileController.php  # Serves files securely
```

## How It Works

### 1. File Upload
- Files are uploaded to the `storage/` directory instead of `public/uploads/`
- The upload path is configured in `.env` file: `UPLOAD_PATH="./storage/"`
- Files are organized in subdirectories by type (logos, products, categories, general)

### 2. File Serving
- Files are served through the `FileController` instead of direct web access
- URLs format: `/storage/folder/filename.ext`
- The FileController handles security, content types, and caching

### 3. Security Features
- **Directory traversal protection**: Prevents access to files outside storage
- **File type validation**: Only allows specific image formats
- **Path sanitization**: Removes dangerous path components
- **No direct access**: Files can't be accessed directly via web URLs

## Benefits

### 🔒 **Security**
- Files are not directly accessible via web URLs
- Protection against directory traversal attacks
- Controlled access through PHP validation
- File type restrictions enforced

### 📁 **Organization**
- Clean separation of public and private files
- Organized folder structure by file type
- No nested public directories

### 🚀 **Performance**
- Proper caching headers (1 year cache)
- Optimized content type detection
- Efficient file serving

### 🔧 **Maintenance**
- Easy to backup (single storage directory)
- Simple to migrate between environments
- Clear file organization

## Configuration

### Environment Variables (`.env`)
```env
# Storage configuration
UPLOAD_PATH="./storage/"
UPLOAD_MAX_SIZE="5242880"
UPLOAD_ALLOWED_TYPES="jpg,jpeg,png,gif,webp"
```

### Production Setup
```env
# Production storage (outside web root)
UPLOAD_PATH="/var/storage/comida-sm/"
```

## Migration

If you have existing files in `public/uploads/`, use the migration script:

```bash
php migrate_files.php
```

This will:
- Move all files from `public/uploads/` to `storage/`
- Organize files in appropriate subdirectories
- Remove empty upload directories
- Update file references

## File Access Examples

### In Templates
```php
<!-- Display establishment logo -->
<?php if (!empty($establishment['logo'])): ?>
    <img src="/<?= $establishment['logo'] ?>" alt="Logo">
<?php endif; ?>

<!-- Display product image -->
<?php if (!empty($product['image'])): ?>
    <img src="/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
<?php endif; ?>
```

### File Paths in Database
Files are stored with paths like:
- `storage/logos/unique_id.jpg`
- `storage/products/unique_id.png`
- `storage/categories/unique_id.webp`

## Security Considerations

### ✅ **Implemented Protections**
- Files stored outside web root
- Path traversal protection
- File type validation
- Access control through PHP

### 🔒 **Additional Recommendations**
- Regularly audit uploaded files
- Monitor storage space usage
- Implement file size limits
- Consider virus scanning for uploads

## Troubleshooting

### File Not Found (404)
- Check if file exists in storage directory
- Verify file path in database
- Ensure FileController route is working

### Permission Errors
- Set proper permissions on storage directory: `chmod 755 storage/`
- Ensure web server can read files: `chown www-data:www-data storage/`

### Slow File Loading
- Verify caching headers are working
- Check file sizes (consider image optimization)
- Monitor server resources

## Development Notes

### File Upload Process
1. File uploaded via form
2. `BaseController::uploadFile()` validates and moves file
3. File path saved to database as `storage/folder/filename.ext`
4. Templates reference file with `/storage/folder/filename.ext`
5. Router directs to `FileController::serve()`
6. FileController validates and serves file

### Adding New File Types
To support additional file types, update:
1. `UPLOAD_ALLOWED_TYPES` in `.env`
2. Content type mapping in `FileController::serve()`

This storage system provides a secure, organized, and efficient way to handle file uploads while maintaining compatibility with the existing application structure.