# target path
$path = "../"
# construct archive path
$DateTime = (Get-Date -Format "yyyyMMddHHmmss")
$destination = "../artifacts\archive-$DateTime.zip"
# exclusion rules. Can use wild cards (*)
$exclude = @(".vscode","publish","artifacts",".gitignore")
# get files to compress using exclusion filer
$files = Get-ChildItem -Path $path -Exclude $exclude
echo $files
New-Item ../artifacts -ItemType Directory -ea 0
# compress
Compress-Archive -Path $files -DestinationPath $destination -CompressionLevel Fastest