# Базовые плагины

Обычно базовые плагины представляют собой общие компоненты, которые обычно устанавливаются с помощью composer и размещаются в каталоге vendor. При установке можно автоматически скопировать некоторые пользовательские настройки (как настройки промежуточного ПО, процессов, маршрутов и др.) в каталог `{основной_проект}config/plugin`, и webman автоматически распознает эту конфигурацию и объединяет ее с основной конфигурацией, что позволяет плагинам вмешиваться в любой этап жизненного цикла webman.

Дополнительные сведения можно найти в [Создание базового плагина](create.md)
