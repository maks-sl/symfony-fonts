# Symfony Fonts - панель управления каталога web-шрифтов.


## Задача: организовать систему хранения файлов шрифтов и управления ими.

Немного предметной области.

```
Web-шрифт обычно представляет собой набор файлов шрифтов с именами в формате 
<имя шрифта>-<имя начертания>.<расширение>
Для каждого начертания могут быть несколько файлов в разных форматах (ttf, eot, woff..)
Как правило, шрифт поставляется с готовым css-файлом для использования в web.
Файлы шрифта + css должны скачиваться одним архивом, в то же время, 
требуется отдельно подгружать их в браузер для демонстрации начертания на web-странице.
```

## Результаты

1) Распаковка zip. Файлы шрифтов можно загрузить как по отдельности, так и архивом.

2) Начертания шрифта. Исходя из названий файлов, система определяет сколько начертаний у шрифта, а также подключает их в стиль страницы для визуализации.

3) Сортировка начертаний. Для лучшего визуального восприятия, добавлена возможность сортировать начертания от тонкого (thin) до жирного (black).

4) Удаление комментариев из css. Иногда в файле стилей могут находиться комментарии со ссылками на ресурс, с которого был скачан шрифт.

5) Упаковка файлов шрифта в архив для скачивания. После того как все необходимые файлы загружены, система сама упакует их в архив для скачивания. Таким образом, в случае будут какие-то исправления в файлах, архив для общедоступного скачивания синхронизируется одной кнопкой. Если zip-файл сформирован, но после были изменения в файлах, система предупреждает что zip "не целостный".