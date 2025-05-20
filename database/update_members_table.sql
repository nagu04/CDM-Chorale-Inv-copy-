-- Add new name columns
ALTER TABLE members
ADD COLUMN last_name VARCHAR(50) AFTER member_id,
ADD COLUMN given_name VARCHAR(50) AFTER last_name,
ADD COLUMN middle_initial CHAR(1) AFTER given_name,
ADD COLUMN extension VARCHAR(10) AFTER middle_initial;

-- Migrate existing data
UPDATE members
SET 
    last_name = SUBSTRING_INDEX(members_name, ',', 1),
    given_name = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 1)),
    middle_initial = CASE 
        WHEN LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 2)) > 0 
        THEN SUBSTRING(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 2)), 1, 1)
        ELSE ''
    END,
    extension = CASE 
        WHEN LENGTH(SUBSTRING_INDEX(members_name, ' ', -1)) > 0 
        AND SUBSTRING_INDEX(members_name, ' ', -1) NOT IN (given_name, middle_initial)
        THEN SUBSTRING_INDEX(members_name, ' ', -1)
        ELSE ''
    END;

-- Drop the old members_name column
ALTER TABLE members DROP COLUMN members_name; 