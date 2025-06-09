<h1>Сервис коротких ссылок + QR</h1>

<h2>Требования к ПО:</h2>
<ul>
  <li>PHP >= 7.4</li>
  <li>MySQL >= 5.7</li>
  <li>Composer</li>
  <li>
    PHP extensions
    <ul>
      <li>pdo_mysql</li>
      <li>curl</li>
    </ul>
  </li>
</ul>

<h2>Инструкция по развертыванию проекта:</h2>
<ul>
  <li>Открыть консоль</li>
  <li>
    Перейти в папку, куда будет копироваться проект:
    <ul>
      <b>cd <путь></b>
    </ul>
  </li>
  <li>
    Создать клон репозитория: 
    <ul>
      <b>git clone https://github.com/vad-dom/test-short-links.git</b>
    </ul>
  </li>
  <li>
    Перейти в папку basic: 
    <ul>
      <b>cd basic</b>
    </ul>
  </li>
  <li>
    Обновить до последних версий и установить зависимости в Composer: 
    <ul>
      <li><b>composer update</b></li>
      <li><b>composer install</b></li>
    </ul>
  </li>
  <li>Создать новую базу данных MySQL</li>
  <li>
    Настроить подключение к базе данных:
    <ul>
      <b>/basic/config/db.php</b>
    </ul>
  </li>
  <li>
    Применить миграцию для создания структуры базы данных:
    <ul>
      <b>php yii migrate</b>
    </ul>
  </li>
</ul>

