-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-02-11 12:37:23
-- 服务器版本： 5.7.20
-- PHP Version: 7.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `bangumi`
--

-- --------------------------------------------------------

--
-- 表的结构 `bd_bangumis`
--

CREATE TABLE `bd_bangumis` (
  `id` int(11) NOT NULL,
  `bangumi_id` int(11) NOT NULL DEFAULT '0',
  `year` int(11) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `titleTranslate` text,
  `title_cn` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `lang` varchar(10) NOT NULL DEFAULT '',
  `officialSite` varchar(200) NOT NULL DEFAULT '',
  `begin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text,
  `sites` text,
  `version` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `bd_options`
--

CREATE TABLE `bd_options` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '',
  `content` varchar(200) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bd_bangumis`
--
ALTER TABLE `bd_bangumis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `version` (`version`),
  ADD KEY `bangumi_id` (`bangumi_id`),
  ADD KEY `year` (`year`),
  ADD KEY `month` (`month`);

--
-- Indexes for table `bd_options`
--
ALTER TABLE `bd_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `bd_bangumis`
--
ALTER TABLE `bd_bangumis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `bd_options`
--
ALTER TABLE `bd_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
