<?php


error_reporting(E_ERROR | E_PARSE);
$mysqli = new mysqli("localhost", "root", "root", "pass");

$TABLE_HEADERS = array(
  "booksTable" => array(
    "id" => "id",
    "Cipher" => "Шифр",
    "Name" => "Название",
    "Circulation" => "Тираж",
    "RealizeDate" => "Дата выхода",
    "SelfCost" => "Себестоимость",
    "SellCost" => "Цена",
    "Fee" => "Гонорар",
    "AuthorsId" => "Ид. Автора",
    "TaskId" => "Ид. Заказа"
  ),
  "adressTable" => array(
    "id" => "id",
    "City" => "Город",
    "Street" => "Улица",
    "Building" => "Дом",
    "Housing" => "Корпус",
    "Appartament" => "Квартира"
  ),
  "authorsTable" => array(
    "id" => "id",
    "PasNumber" => "Номер паспорта",
    "SecondName" => "Фамилия",
    "Name" => "Имя",
    "FathersName" => "Отчество",
    "Phone" => "Телефонный номер",
  ),
  "contastfaceTable" => array(
    "id" => "id",
    "Name" => "Имя",
    "SecondName" => "Фамилия",
    "FathersName" => "Отчество"
  ),
  "contractsTable" => array(
    "id" => "id",
    "Number" => "Номер",
    "ContractDate" => "Дата заключения",
    "ContractTerm" => "Срок действия",
    "Status" => "Статус",
    "ContractTerminationDate" => "Дата окончания",
    "AuthorId" => "Ид. Автора"
  ),
  "customersTable" => array(
    "id" => "id",
    "Name" => "Наименование",
    "Phone" => "Телефонный номер",
    "AdressId" => "Ид. Адреса",
    "TasksId" => "Ид. заказа"
  ),
  "tasksTable" => array(
    "id" => "id",
    "TaskNumber" => "Номер заказа",
    "DateReceiptTask" => "Дата поступления заказа",
    "DateRealizeTask" => "Дата выполнения заказа",
    "BooksCount" => "Количество книг",
    "BooksId" => "Ид. Книги",
    "CustormersId" => "Ид. Заказчика"
  )
);
