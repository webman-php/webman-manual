# 環境要求


## Linux 系統
Linux 系統需要 `posix` 和 `pcntl` 擴展，這兩個擴展是 PHP 內建的，通常情況下無需安裝即可使用。

如果您使用寶塔面板，只需在寶塔中禁用或刪除以 `pnctl_` 開頭的函數即可。

`event` 擴展並非必要，但是為了更好的性能建議安裝此擴展。


## Windows 系統
Webman 可在 Windows 系統上運行，但由於無法設置多進程、守護進程等原因，建議將 Windows 僅作為開發環境使用，生產環境請使用 Linux 系統。

注意：Windows 系統下不依賴 `posix` 和 `pcntl` 擴展。
