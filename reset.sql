DROP DATABASE IF EXIST oweni;
CREATE DATABASE IF NOT EXIST oweni;
USE oweni;


--
-- База данных: `oweni`
--

-- --------------------------------------------------------

--
-- Структура таблицы `account`
--

CREATE TABLE `account` (
  `id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
);

-- --------------------------------------------------------

--
-- Структура таблицы `case`
--

CREATE TABLE `case` (
  `id` int NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL,
  `profile_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Структура таблицы `education`
--

CREATE TABLE `education` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `income` date NOT NULL,
  `release` date NOT NULL,
  `branch` varchar(50) NOT NULL,
  `profile_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Структура таблицы `medical`
--

CREATE TABLE `medical` (
  `id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `result` varchar(100) NOT NULL,
  `case_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Структура таблицы `profile`
--

CREATE TABLE `profile` (
  `id` int NOT NULL,
  `position` varchar(50) NOT NULL,
  `living_address` varchar(200) NOT NULL,
  `off_address` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `private_phone` varchar(50) NOT NULL,
  `home_phone` varchar(50) DEFAULT NULL,
  `languages` varchar(50) DEFAULT NULL,
  `hobby` varchar(50) DEFAULT NULL,
  `citizenship` varchar(50) NOT NULL
);

-- --------------------------------------------------------

--
-- Структура таблицы `relative`
--

CREATE TABLE `relative` (
  `id` int NOT NULL,
  `role` varchar(50) NOT NULL,
  `work_place` varchar(200) NOT NULL,
  `birthday` date NOT NULL,
  `name` varchar(100) NOT NULL,
  `profile_id` int NOT NULL
);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `case`
--
ALTER TABLE `case`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Индексы таблицы `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Индексы таблицы `medical`
--
ALTER TABLE `medical`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`);

--
-- Индексы таблицы `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `relative`
--
ALTER TABLE `relative`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `account`
--
ALTER TABLE `account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `case`
--
ALTER TABLE `case`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `education`
--
ALTER TABLE `education`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `medical`
--
ALTER TABLE `medical`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `relative`
--
ALTER TABLE `relative`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `case`
--
ALTER TABLE `case`
  ADD CONSTRAINT `case_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `medical`
--
ALTER TABLE `medical`
  ADD CONSTRAINT `medical_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `relative`
--
ALTER TABLE `relative`
  ADD CONSTRAINT `relative_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE;

