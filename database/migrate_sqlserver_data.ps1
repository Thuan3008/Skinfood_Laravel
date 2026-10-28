$ErrorActionPreference = 'Stop'
$tables = @('DanhMuc','ThuongHieu','LoaiSP','NguoiDung','NhaCungCap','SanPham','KhuyenMai','KhuyenMai_SanPham','TaiKhoan','DonHang','ChiTietDonHangs','DanhGia','LienHe','PhieuNhap','ChiTietPhieuNhap','PhieuXuat','ChiTietPhieuXuat')
$connectionString = 'Server=DAUCATMOI\SQLEXPRESS;Database=DB_SkinFood_PHP;Integrated Security=True;TrustServerCertificate=True;'
$mysql = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe'
$sqlPath = Join-Path $env:TEMP 'skinfood-data-import.sql'
$cn = New-Object System.Data.SqlClient.SqlConnection $connectionString
$cn.Open()
$sb = New-Object System.Text.StringBuilder
[void]$sb.AppendLine('SET FOREIGN_KEY_CHECKS=0;')
foreach ($table in $tables) {
    $cmd = $cn.CreateCommand()
    $cmd.CommandText = "SELECT * FROM dbo.[$table]"
    $reader = $cmd.ExecuteReader()
    $schema = $reader.GetSchemaTable()
    $columns = @($schema | ForEach-Object { $_.ColumnName })
    while ($reader.Read()) {
        $values = foreach ($i in 0..($reader.FieldCount - 1)) {
            if ($reader.IsDBNull($i)) { 'NULL'; continue }
            $value = $reader.GetValue($i)
            if ($value -is [bool]) { if ($value) { '1' } else { '0' }; continue }
            if ($value -is [DateTime]) { "'" + $value.ToString('yyyy-MM-dd HH:mm:ss.fff') + "'"; continue }
            if ($value -is [byte[]]) { "X'" + ([BitConverter]::ToString($value) -replace '-', '') + "'"; continue }
            $text = [string]$value
            "'" + ($text -replace '\\', '\\\\' -replace "'", "''") + "'"
        }
        $columnSql = ($columns | ForEach-Object { "``$_``" }) -join ','
        [void]$sb.AppendLine("INSERT INTO ``$table`` ($columnSql) VALUES (" + ($values -join ',') + ');')
    }
    $reader.Close()
}
$cn.Close()
[void]$sb.AppendLine('SET FOREIGN_KEY_CHECKS=1;')
[IO.File]::WriteAllText($sqlPath, $sb.ToString(), [Text.Encoding]::UTF8)
& $mysql --host=localhost --port=3306 --user=root DB_SkinFood1 --execute="SET FOREIGN_KEY_CHECKS=0;" | Out-Null
Get-Content -Raw $sqlPath | & $mysql --host=localhost --port=3306 --user=root DB_SkinFood1 --force
if ($LASTEXITCODE -ne 0) { throw "MySQL import failed with exit code $LASTEXITCODE" }
Write-Output "Imported SQL Server data into DB_SkinFood1 from $sqlPath"
