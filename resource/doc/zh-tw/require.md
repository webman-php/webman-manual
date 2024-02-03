# 所需環境


## Linux系統
Linux系統需要 `posix` 和 `pcntl` 擴展支持，這兩個擴展都是PHP內建的，通常無需安裝即可使用。

如果您使用宝塔面板，只需在面板中禁用或刪除以 `pnctl_` 開頭的函數。

雖然不是必要的，但建議安裝 `event` 擴展以獲得更好的性能。

## Windows系統
Webman 可以在Windows系統上運行，但由於無法設置多進程、守護程序等功能，建議將Windows僅用於開發環境，生產環境應使用Linux系統。

注意：Windows系統不依賴 `posix` 和 `pcntl` 擴展。