<?
$MESS["ACRIT_EXP_RUNNOW_START"] = "Запустить загрузку";
$MESS["ACRIT_EXP_RUNNOW_STOP"] = "Остановить";
$MESS["ACRIT_EXP_VSEGO_OBRABOTANO"] = "Всего обработано: ";
$MESS["ACRIT_EXP_USPESNO_IMPORTIROVAN"] = "Успешно импортировано: ";
$MESS["ACRIT_EXP_PROPUSENO"] = "Пропущено: ";
$MESS["ACRIT_EXP_S_OSIBKAMI"] = "С ошибками: ";
$MESS["ACRIT_EXP_AGENTS_EDIT"] = "Изменить";
$MESS["ACRIT_EXP_AGENTS_DEL"] = "Удалить";
$MESS["ACRIT_EXP_AGENTS_DEL_WARN"] = "Удалить запись?";
$MESS["ACRIT_EXP_AGENTS_TITLES_ID"] = "ID";
$MESS["ACRIT_EXP_AGENTS_TITLES_INTERVAL"] = "Интервал";
$MESS["ACRIT_EXP_AGENTS_TITLES_LAST_START"] = "Последний запуск";
$MESS["ACRIT_EXP_AGENTS_TITLES_NEXT_START"] = "Следующий запуск";
$MESS["ACRIT_EXP_AGENTS_AGENTS"] = "Добавить";
$MESS["ACRIT_EXP_AGENTS_SERVER_TIME"] = "Текущее время на сервере: ";
$MESS["ACRIT_CRM_TAB_SYNC_HEADING"] = "Ручной запуск синхронизации";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TITLE"] = "Загрузить заказы, созданные";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TERM_ALL"] = "За всё время";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TERM_3M"] = "За последние три месяца";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TERM_1M"] = "За последний месяц";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TERM_1W"] = "За последние 7 дней";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_TERM_1D"] = "За последние сутки";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_ONLY_NEW"] = "Пропускать уже загруженные в интернет-магазин заказы";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_RUN_TITLE"] = "Запустить загрузку данных заказов";
$MESS["ACRIT_CRM_TAB_SYNC_RANGE_TITLE"] = "Общие настройки фоновой синхронизации";
$MESS["ACRIT_CRM_TAB_SYNC_CRON_TITLE"] = "Фоновая синхронизация через CRON";
$MESS['ACRIT_CRM_TAB_SYNC_CRON_HINT'] = 'В данном поле представлена команда для выполнения планировщиком.<br/><br/>Данная команда сгенерирована по общим правилам, поэтому на некоторых серверах может понадобиться внести в нее небольшие корректировки. Например, может понадобиться указать свой путь к PHP (здесь выводится указанный в настройках модуля путь к PHP) или добавить/убрать из команды настройки mbstring - это можно сделать в <a href="/bitrix/admin/settings.php?lang=ru&mid=acrit.core" target="_blank">настройках модуля ядра Акрит</a>.<br/><br/>Данная команда может быть запущена из SSH, что удобно в некоторых случаях.';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_WARNING'] = '<span style="color:red"><b>Внимание!</b> Никогда не запускайте скрипты от имени root! Т.к. если в процессе работы создаются какие-либо файлы и папки, они создаются от имени root, и в последующем сайт не сможет корректно работать с этими файлами и папками, будут постоянные ошибки.</span>';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_COPY'] = 'Копировать';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_COPY_SUCCESS'] = 'Команда скопирована в буфер обмена!';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_LINK_ARTICLE_BITRIX_ENV'] = '<a href="https://www.acrit-studio.ru/pantry-programmer/bitrix-bitrix-environment-vm/crond-in-bitrix-vm/" target="_blank">Информация по настройке Cron на виртуальной машине Битрикс</a>';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_SERVER_TIME'] = 'Время сервера';
$MESS["ACRIT_CRM_TAB_SYNC_AGENTS_TITLE"] = "Фоновая синхронизация с помощью агентов";
$MESS["ACRIT_CRM_TAB_LOG_TITLE"] = "Лог профиля";
$MESS["ACRIT_CRM_TAB_SYNC_RANGE"] = "Охватываемый период синхронизации (мин):";
$MESS["ACRIT_CRM_TAB_SYNC_RANGE_HINT"] = "За какое время будут обрабатываться заказы при каждом запуске синхронизации.";
$MESS['ACRIT_CRM_TAB_SYNC_RANGE_1'] = 'Охватываемый период основной синхронизации:';
$MESS['ACRIT_CRM_TAB_SYNC_RANGE_1_HINT'] = 'За какое время будут обрабатываться заказы при каждом запуске основной синхронизации. Основная синхронизация отвечает за оперативное получение данных по заказам.';
$MESS['ACRIT_CRM_TAB_SYNC_RANGE_2'] = 'Охватываемый период дополнительной синхронизации:';
$MESS['ACRIT_CRM_TAB_SYNC_RANGE_2_HINT'] = 'За какое время будут обрабатываться заказы при каждом запуске дополнительной синхронизации. Дополнительная синхронизация - это вспомогательный механизм, на случай если основная синхронизация пропустить какие-либо изменения.';
$MESS["ACRIT_CRM_TAB_SYNC_AGENTS_ACTIVE"] = "Активировать агентов";
$MESS["ACRIT_CRM_TAB_SYNC_CRON_PERIOD"] = "Команда для запуска синхронизации";
$MESS['ACRIT_CRM_TAB_SYNC_CRON_PERIOD_1'] = 'Команда для запуска основной синхронизации';
$MESS['ACRIT_CRM_TAB_SYNC_CRON_PERIOD_2'] = 'Команда для запуска дополнительной синхронизации';
$MESS["ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD"] = "Частота запуска агента (мин):";
$MESS['ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD_1'] = 'Запуск агента основной синхронизации: каждые';
$MESS['ACRIT_CRM_TAB_SYNC_AGENTS_PERIOD_2'] = 'Запуск агента дополнительной синхронизации: каждые';
$MESS['ACRIT_CRM_TAB_SYNC_AGENTS_HOURS'] = 'часов';
$MESS['ACRIT_CRM_TAB_SYNC_AGENTS_MINUTES'] = 'минут';
$MESS["ACRIT_CRM_TAB_SYNC_MAN_ALL"] = "Доступно заказов для загрузки в интернет-магазин:";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_DONE"] = "Загружено заказов:";
$MESS["ACRIT_CRM_TAB_SYNC_MAN_DONE_2"] = "Загружено в интернет-магазин заказов:";
?>