# 環境需求


## Linux系統
Linux系統需要依賴`posix`和`pcntl`擴展，這兩個擴展都是內建在PHP中的，通常情況下無需安裝即可使用。

如果您使用寶塔面板，只需在寶塔中禁用或刪除以`pnctl_`開頭的函數。

`event`擴展則非必要，但為了獲得更好的性能，建議安裝此擴展。

## Windows系統
Webman可以在Windows系統上運行，但由於無法設置多進程、守護進程等功能限制，建議將Windows僅用作開發環境，生產環境請使用Linux系統。

注意：在Windows系統下無需依賴`posix`和`pcntl`擴展。