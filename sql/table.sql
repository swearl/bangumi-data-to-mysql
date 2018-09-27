-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-09-27 18:04:27
-- 服务器版本： 5.7.20
-- PHP Version: 7.0.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `bangumi`
--

-- --------------------------------------------------------

--
-- 表的结构 `bangumi_data_items`
--

CREATE TABLE `bangumi_data_items` (
  `id` int(11) NOT NULL,
  `bangumi_id` int(11) NOT NULL DEFAULT '0',
  `bilibili_id` int(11) NOT NULL DEFAULT '0',
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
  `update_day` int(11) NOT NULL DEFAULT '0',
  `update_type` int(11) NOT NULL DEFAULT '1',
  `ended` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `bangumi_data_options`
--

CREATE TABLE `bangumi_data_options` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '',
  `content` varchar(200) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `bangumi_data_sites`
--

CREATE TABLE `bangumi_data_sites` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `urlTemplate` varchar(250) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bangumi_data_items`
--
ALTER TABLE `bangumi_data_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `version` (`version`),
  ADD KEY `bangumi_id` (`bangumi_id`),
  ADD KEY `year` (`year`),
  ADD KEY `month` (`month`),
  ADD KEY `type` (`type`),
  ADD KEY `lang` (`lang`),
  ADD KEY `ended` (`ended`),
  ADD KEY `update_day` (`update_day`),
  ADD KEY `update_type` (`update_type`),
  ADD KEY `bilibili_id` (`bilibili_id`);

--
-- Indexes for table `bangumi_data_options`
--
ALTER TABLE `bangumi_data_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `bangumi_data_sites`
--
ALTER TABLE `bangumi_data_sites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `type` (`type`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `bangumi_data_items`
--
ALTER TABLE `bangumi_data_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `bangumi_data_options`
--
ALTER TABLE `bangumi_data_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `bangumi_data_sites`
--
ALTER TABLE `bangumi_data_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
