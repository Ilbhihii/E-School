param(
    [Parameter(Mandatory=$true)][string]$Email,
    [Parameter(Mandatory=$true)][string]$Password
)

$ErrorActionPreference = 'Stop'
$chrome = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$profile = 'F:\backend\.presentation-chrome-profile'
$port = 9223
Start-Process -FilePath $chrome -ArgumentList "--headless=new", "--remote-debugging-port=$port", "--user-data-dir=$profile", '--no-first-run', '--disable-gpu', 'about:blank' -WindowStyle Hidden

for ($i = 0; $i -lt 30; $i++) {
    try { $targets = @(Invoke-RestMethod "http://127.0.0.1:$port/json/list"); $target = $targets[0]; if ($target.webSocketDebuggerUrl) { break } } catch {}
    Start-Sleep -Milliseconds 300
}
if (-not $target.webSocketDebuggerUrl) { throw 'Chrome DevTools indisponible.' }

$socket = [System.Net.WebSockets.ClientWebSocket]::new()
$socket.ConnectAsync([Uri]$target.webSocketDebuggerUrl, [Threading.CancellationToken]::None).GetAwaiter().GetResult()
$script:commandId = 0
function Invoke-CDP($method, $params = @{}) {
    $script:commandId++
    $id = $script:commandId
    $payload = @{id=$id; method=$method; params=$params} | ConvertTo-Json -Compress -Depth 8
    $bytes = [Text.Encoding]::UTF8.GetBytes($payload)
    $socket.SendAsync([ArraySegment[byte]]::new($bytes), [Net.WebSockets.WebSocketMessageType]::Text, $true, [Threading.CancellationToken]::None).GetAwaiter().GetResult()
    do {
        $buffer = [byte[]]::new(10485760)
        $result = $socket.ReceiveAsync([ArraySegment[byte]]::new($buffer), [Threading.CancellationToken]::None).GetAwaiter().GetResult()
        $json = [Text.Encoding]::UTF8.GetString($buffer, 0, $result.Count) | ConvertFrom-Json
    } while ($json.id -ne $id)
    if ($json.error) { throw $json.error.message }
    return $json.result
}

Invoke-CDP 'Page.enable' | Out-Null
Invoke-CDP 'Page.navigate' @{url='http://127.0.0.1:8000/login'} | Out-Null
Start-Sleep -Seconds 2
$emailJson = $Email | ConvertTo-Json -Compress
$passwordJson = $Password | ConvertTo-Json -Compress
$expr = "document.querySelector('input[name=email]').value=$emailJson; document.querySelector('input[name=password]').value=$passwordJson; document.querySelector('form').submit(); 'submitted';"
Invoke-CDP 'Runtime.evaluate' @{expression=$expr} | Out-Null
Start-Sleep -Seconds 4
$capture = Invoke-CDP 'Page.captureScreenshot' @{format='png'; captureBeyondViewport=$true}
[IO.File]::WriteAllBytes('F:\backend\public\presentation-authenticated-dashboard.png', [Convert]::FromBase64String($capture.data))
$url = (Invoke-CDP 'Runtime.evaluate' @{expression='location.href'}).result.value
$title = (Invoke-CDP 'Runtime.evaluate' @{expression='document.title'}).result.value
$socket.Dispose()
Write-Output "Capture enregistrée. URL: $url | Titre: $title"
