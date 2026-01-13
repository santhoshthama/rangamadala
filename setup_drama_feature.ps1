# Quick Setup Script for Drama Creation Feature
# Run this in PowerShell

Write-Host "Setting up Drama Creation Feature..." -ForegroundColor Green

# Create upload directory
$uploadDir = "app\uploads\drama_images"
if (-not (Test-Path $uploadDir)) {
    New-Item -ItemType Directory -Force -Path $uploadDir
    Write-Host "✓ Created upload directory: $uploadDir" -ForegroundColor Green
} else {
    Write-Host "✓ Upload directory already exists: $uploadDir" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Run the database migration SQL in your MySQL database:"
Write-Host "   File: database_migration_creator_artist.sql" -ForegroundColor White
Write-Host ""
Write-Host "2. You can run it via command line:" -ForegroundColor Yellow
Write-Host "   mysql -u root -p rangamandala_db < database_migration_creator_artist.sql" -ForegroundColor White
Write-Host ""
Write-Host "3. Or via phpMyAdmin:" -ForegroundColor Yellow
Write-Host "   - Open phpMyAdmin"
Write-Host "   - Select 'rangamandala_db' database"
Write-Host "   - Click 'SQL' tab"
Write-Host "   - Copy and paste contents of database_migration_creator_artist.sql"
Write-Host "   - Click 'Go'"
Write-Host ""
Write-Host "4. Test the feature:" -ForegroundColor Yellow
Write-Host "   - Login as an artist"
Write-Host "   - Click 'Create Drama' button"
Write-Host "   - Fill the form and submit"
Write-Host ""
Write-Host "Setup script completed!" -ForegroundColor Green
