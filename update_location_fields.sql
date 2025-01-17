-- Add new location columns to tbluserdetails table
ALTER TABLE tbluserdetails 
ADD COLUMN division VARCHAR(50) NOT NULL AFTER occupation,
ADD COLUMN district VARCHAR(50) NOT NULL AFTER division,
ADD COLUMN upazila VARCHAR(50) NOT NULL AFTER district,
ADD COLUMN pouroshova VARCHAR(100) NOT NULL AFTER upazila;

-- Update existing records with default values
UPDATE tbluserdetails 
SET division = 'Dhaka',
    district = 'Dhaka',
    upazila = 'Dhaka',
    pouroshova = 'Dhaka'
WHERE division IS NULL; 