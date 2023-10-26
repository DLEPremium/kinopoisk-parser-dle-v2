# kinopoisk-parser-dle-v2
Бесплатный парсер КиноПоиска для CMS DataLife Engine (DLE). Парсинг осуществляется с помощью сервиса https://kinopoisk.dev/. Поддерживаемые версии DLE - 15.0-17.0

# Установка:
1. Установить плагин parser-kinopoisk.xml в системе управления плагинами.
2. Загрузить файлы в корень сайта.
3. Настроить модуль в админке DLE.

# Как правильно обновлять:
Загружаем все папки и файлы с заменой КРОМЕ папки engine/dlepremium/kinopoisk_parser/data/, в ней содержаться ваши настройки модуля, перезалив её настройки обнулятся!

# Важно:
Данный модуль создан на основе предыдущего модуля https://github.com/DLEPremium/kinopoisk-parser-dle. Их одновременное использование невозможно. Если вы пользовались прошлым модулем, то вам нужно перезалить все файлы модуля и внести настройки в админку.

# История обновлений:
1. 26.10.2023 - релиз модуля