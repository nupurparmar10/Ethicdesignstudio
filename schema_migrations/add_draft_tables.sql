CREATE TABLE `sale_drafts` (
  `draft_id` VARCHAR(36) PRIMARY KEY,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  INDEX (`expires_at`)
);

CREATE TABLE `sale_draft_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `draft_id` VARCHAR(36) NOT NULL,
  `v_id` INT NOT NULL,
  `qty` INT NOT NULL,
  FOREIGN KEY (`draft_id`) REFERENCES `sale_drafts`(`draft_id`) ON DELETE CASCADE,
  UNIQUE KEY `draft_item_uniq` (`draft_id`, `v_id`)
);
