##Wiseci cli tool##

###Введение###

**Wiseci** небольшой консольный скрипт для Codeigniter <= 3.x. Он может создавать контроллеры, модели,
миграции на основе шаблонов. Также можно скрипт расширять своими коммандами и использовать механизм 
миграций Codeigniter.
Скрипт был написан на волне впечатлений от Laravel Artisan, а так же черезжопной реализации работы с
миграциями в Codeigniter.

###Использование###

Для использования необходимо разместить директорию Wisecore и сам скрипт wiseci в корне фреймворка.
Если вы решили разместить в директорию отличную от корня фреймворка, то нужно изменить в скрипте
константы путей.

***Важно***
Не рекоммендуется использовать в продакшене. Но если этого не избежать, то необходимо запретить
вашему веб-сервер отдавать данный файл.
Заглушка для apache 
*.htaccess*
    <Files wiseci>
        deny from all
    </Files>

**Список** комманд доступных изначально можно получить через ключ <--list> 

    *php wiseci --list*

Чтобы создать свой набор комманд используте:
    
    *php wiseci make:console --name=YouCommandName*

Созданнаый таким образом класс комманды можно найти в директории Wisecore/Commands
    
###Дисклеймер###
Данный скрипт написан лишь для собственного удовлетвория. Тестами не покрыт. Всю отвественость за
возможные факапы вы берете на себя.