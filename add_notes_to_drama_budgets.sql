-- Ensure drama_budgets has notes column for Manage Budget feature
ALTER TABLE `drama_budgets`
ADD COLUMN IF NOT EXISTS `notes` text DEFAULT NULL AFTER `status`;