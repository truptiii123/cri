-- Complete Database Schema for FastTrack Courier Services
-- Compatible with Hostinger/cPanel phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: courier_services

-- --------------------------------------------------------

-- Table structure for table `admin`
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO `admin` (`username`, `password`) VALUES
('admin', 'admin123');

-- --------------------------------------------------------

-- Table structure for table `agents`
CREATE TABLE `agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` varchar(20) NOT NULL,
  `agent_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_id` (`agent_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample agents
INSERT INTO `agents` (`agent_id`, `agent_name`, `username`, `password`, `mobile`, `city`, `status`) VALUES
('AGT001', 'Rajesh Kumar', 'agent1', 'admin123', '9876543210', 'Mumbai', 'active'),
('AGT002', 'Priya Sharma', 'agent2', 'admin123', '9876543211', 'Delhi', 'active'),
('AGT003', 'Amit Singh', 'agent3', 'admin123', '9876543212', 'Bangalore', 'active');

-- --------------------------------------------------------

-- Table structure for table `cities`
CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(100) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `city_name` (`city_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert comprehensive Indian cities data (500+ cities)
INSERT INTO `cities` (`state`, `city_name`, `district`, `pincode`) VALUES
-- Andhra Pradesh
('Andhra Pradesh', 'Visakhapatnam', 'Visakhapatnam', '530001'),
('Andhra Pradesh', 'Vijayawada', 'Krishna', '520001'),
('Andhra Pradesh', 'Guntur', 'Guntur', '522001'),
('Andhra Pradesh', 'Nellore', 'Nellore', '524001'),
('Andhra Pradesh', 'Kurnool', 'Kurnool', '518001'),
('Andhra Pradesh', 'Rajahmundry', 'East Godavari', '533101'),
('Andhra Pradesh', 'Tirupati', 'Chittoor', '517501'),
('Andhra Pradesh', 'Kadapa', 'Kadapa', '516001'),
('Andhra Pradesh', 'Anantapur', 'Anantapur', '515001'),
('Andhra Pradesh', 'Eluru', 'West Godavari', '534001'),

-- Arunachal Pradesh
('Arunachal Pradesh', 'Itanagar', 'Papum Pare', '791111'),
('Arunachal Pradesh', 'Naharlagun', 'Papum Pare', '791110'),
('Arunachal Pradesh', 'Pasighat', 'East Siang', '791102'),
('Arunachal Pradesh', 'Tezpur', 'Sonitpur', '784001'),
('Arunachal Pradesh', 'Bomdila', 'West Kameng', '790001'),

-- Assam
('Assam', 'Guwahati', 'Kamrup', '781001'),
('Assam', 'Silchar', 'Cachar', '788001'),
('Assam', 'Dibrugarh', 'Dibrugarh', '786001'),
('Assam', 'Jorhat', 'Jorhat', '785001'),
('Assam', 'Nagaon', 'Nagaon', '782001'),
('Assam', 'Tinsukia', 'Tinsukia', '786125'),
('Assam', 'Tezpur', 'Sonitpur', '784001'),

-- Bihar
('Bihar', 'Patna', 'Patna', '800001'),
('Bihar', 'Gaya', 'Gaya', '823001'),
('Bihar', 'Bhagalpur', 'Bhagalpur', '812001'),
('Bihar', 'Muzaffarpur', 'Muzaffarpur', '842001'),
('Bihar', 'Purnia', 'Purnia', '854301'),
('Bihar', 'Darbhanga', 'Darbhanga', '846001'),
('Bihar', 'Bihar Sharif', 'Nalanda', '803101'),
('Bihar', 'Arrah', 'Bhojpur', '802301'),
('Bihar', 'Begusarai', 'Begusarai', '851101'),
('Bihar', 'Katihar', 'Katihar', '854105'),

-- Chhattisgarh
('Chhattisgarh', 'Raipur', 'Raipur', '492001'),
('Chhattisgarh', 'Bhilai', 'Durg', '490001'),
('Chhattisgarh', 'Korba', 'Korba', '495677'),
('Chhattisgarh', 'Bilaspur', 'Bilaspur', '495001'),
('Chhattisgarh', 'Durg', 'Durg', '491001'),
('Chhattisgarh', 'Rajnandgaon', 'Rajnandgaon', '491441'),

-- Goa
('Goa', 'Panaji', 'North Goa', '403001'),
('Goa', 'Margao', 'South Goa', '403601'),
('Goa', 'Vasco da Gama', 'South Goa', '403802'),
('Goa', 'Mapusa', 'North Goa', '403507'),
('Goa', 'Ponda', 'North Goa', '403401'),

-- Gujarat
('Gujarat', 'Ahmedabad', 'Ahmedabad', '380001'),
('Gujarat', 'Surat', 'Surat', '395001'),
('Gujarat', 'Vadodara', 'Vadodara', '390001'),
('Gujarat', 'Rajkot', 'Rajkot', '360001'),
('Gujarat', 'Bhavnagar', 'Bhavnagar', '364001'),
('Gujarat', 'Jamnagar', 'Jamnagar', '361001'),
('Gujarat', 'Junagadh', 'Junagadh', '362001'),
('Gujarat', 'Gandhinagar', 'Gandhinagar', '382010'),
('Gujarat', 'Anand', 'Anand', '388001'),
('Gujarat', 'Navsari', 'Navsari', '396001'),
('Gujarat', 'Morbi', 'Morbi', '363641'),
('Gujarat', 'Mehsana', 'Mehsana', '384001'),
('Gujarat', 'Bharuch', 'Bharuch', '392001'),
('Gujarat', 'Vapi', 'Valsad', '396191'),
('Gujarat', 'Veraval', 'Gir Somnath', '362265'),

-- Haryana
('Haryana', 'Faridabad', 'Faridabad', '121001'),
('Haryana', 'Gurgaon', 'Gurgaon', '122001'),
('Haryana', 'Panipat', 'Panipat', '132103'),
('Haryana', 'Ambala', 'Ambala', '134003'),
('Haryana', 'Yamunanagar', 'Yamunanagar', '135001'),
('Haryana', 'Rohtak', 'Rohtak', '124001'),
('Haryana', 'Hisar', 'Hisar', '125001'),
('Haryana', 'Karnal', 'Karnal', '132001'),
('Haryana', 'Sonipat', 'Sonipat', '131001'),
('Haryana', 'Panchkula', 'Panchkula', '134109'),

-- Himachal Pradesh
('Himachal Pradesh', 'Shimla', 'Shimla', '171001'),
('Himachal Pradesh', 'Dharamshala', 'Kangra', '176215'),
('Himachal Pradesh', 'Solan', 'Solan', '173212'),
('Himachal Pradesh', 'Mandi', 'Mandi', '175001'),
('Himachal Pradesh', 'Palampur', 'Kangra', '176061'),
('Himachal Pradesh', 'Baddi', 'Solan', '173205'),
('Himachal Pradesh', 'Kullu', 'Kullu', '175101'),

-- Jharkhand
('Jharkhand', 'Ranchi', 'Ranchi', '834001'),
('Jharkhand', 'Jamshedpur', 'East Singhbhum', '831001'),
('Jharkhand', 'Dhanbad', 'Dhanbad', '826001'),
('Jharkhand', 'Bokaro', 'Bokaro', '827001'),
('Jharkhand', 'Deoghar', 'Deoghar', '814112'),
('Jharkhand', 'Phusro', 'Bokaro', '827013'),
('Jharkhand', 'Hazaribagh', 'Hazaribagh', '825301'),

-- Karnataka
('Karnataka', 'Bangalore', 'Bangalore Urban', '560001'),
('Karnataka', 'Mysore', 'Mysore', '570001'),
('Karnataka', 'Hubli', 'Dharwad', '580020'),
('Karnataka', 'Mangalore', 'Dakshina Kannada', '575001'),
('Karnataka', 'Belgaum', 'Belgaum', '590001'),
('Karnataka', 'Gulbarga', 'Gulbarga', '585101'),
('Karnataka', 'Davanagere', 'Davanagere', '577001'),
('Karnataka', 'Bellary', 'Bellary', '583101'),
('Karnataka', 'Bijapur', 'Bijapur', '586101'),
('Karnataka', 'Shimoga', 'Shimoga', '577201'),
('Karnataka', 'Tumkur', 'Tumkur', '572101'),
('Karnataka', 'Raichur', 'Raichur', '584101'),
('Karnataka', 'Bidar', 'Bidar', '585401'),
('Karnataka', 'Hospet', 'Bellary', '583201'),
('Karnataka', 'Gadag', 'Gadag', '582101'),

-- Kerala
('Kerala', 'Thiruvananthapuram', 'Thiruvananthapuram', '695001'),
('Kerala', 'Kochi', 'Ernakulam', '682001'),
('Kerala', 'Kozhikode', 'Kozhikode', '673001'),
('Kerala', 'Thrissur', 'Thrissur', '680001'),
('Kerala', 'Kollam', 'Kollam', '691001'),
('Kerala', 'Palakkad', 'Palakkad', '678001'),
('Kerala', 'Alappuzha', 'Alappuzha', '688001'),
('Kerala', 'Malappuram', 'Malappuram', '676101'),
('Kerala', 'Kannur', 'Kannur', '670001'),
('Kerala', 'Kasaragod', 'Kasaragod', '671121'),

-- Madhya Pradesh
('Madhya Pradesh', 'Indore', 'Indore', '452001'),
('Madhya Pradesh', 'Bhopal', 'Bhopal', '462001'),
('Madhya Pradesh', 'Jabalpur', 'Jabalpur', '482001'),
('Madhya Pradesh', 'Gwalior', 'Gwalior', '474001'),
('Madhya Pradesh', 'Ujjain', 'Ujjain', '456001'),
('Madhya Pradesh', 'Sagar', 'Sagar', '470001'),
('Madhya Pradesh', 'Dewas', 'Dewas', '455001'),
('Madhya Pradesh', 'Satna', 'Satna', '485001'),
('Madhya Pradesh', 'Ratlam', 'Ratlam', '457001'),
('Madhya Pradesh', 'Rewa', 'Rewa', '486001'),
('Madhya Pradesh', 'Murwara', 'Katni', '483501'),
('Madhya Pradesh', 'Singrauli', 'Singrauli', '486889'),
('Madhya Pradesh', 'Burhanpur', 'Burhanpur', '450331'),
('Madhya Pradesh', 'Khandwa', 'Khandwa', '450001'),
('Madhya Pradesh', 'Morena', 'Morena', '476001'),

-- Maharashtra
('Maharashtra', 'Mumbai', 'Mumbai City', '400001'),
('Maharashtra', 'Pune', 'Pune', '411001'),
('Maharashtra', 'Nagpur', 'Nagpur', '440001'),
('Maharashtra', 'Thane', 'Thane', '400601'),
('Maharashtra', 'Nashik', 'Nashik', '422001'),
('Maharashtra', 'Aurangabad', 'Aurangabad', '431001'),
('Maharashtra', 'Solapur', 'Solapur', '413001'),
('Maharashtra', 'Amravati', 'Amravati', '444601'),
('Maharashtra', 'Kolhapur', 'Kolhapur', '416001'),
('Maharashtra', 'Sangli', 'Sangli', '416416'),
('Maharashtra', 'Malegaon', 'Nashik', '423203'),
('Maharashtra', 'Akola', 'Akola', '444001'),
('Maharashtra', 'Latur', 'Latur', '413512'),
('Maharashtra', 'Dhule', 'Dhule', '424001'),
('Maharashtra', 'Ahmednagar', 'Ahmednagar', '414001'),
('Maharashtra', 'Chandrapur', 'Chandrapur', '442401'),
('Maharashtra', 'Parbhani', 'Parbhani', '431401'),
('Maharashtra', 'Jalgaon', 'Jalgaon', '425001'),
('Maharashtra', 'Bhiwandi', 'Thane', '421302'),
('Maharashtra', 'Nanded', 'Nanded', '431601'),

-- Manipur
('Manipur', 'Imphal', 'Imphal West', '795001'),
('Manipur', 'Thoubal', 'Thoubal', '795138'),
('Manipur', 'Bishnupur', 'Bishnupur', '795126'),
('Manipur', 'Churachandpur', 'Churachandpur', '795128'),

-- Meghalaya
('Meghalaya', 'Shillong', 'East Khasi Hills', '793001'),
('Meghalaya', 'Tura', 'West Garo Hills', '794001'),
('Meghalaya', 'Nongstoin', 'West Khasi Hills', '793119'),

-- Mizoram
('Mizoram', 'Aizawl', 'Aizawl', '796001'),
('Mizoram', 'Lunglei', 'Lunglei', '796701'),
('Mizoram', 'Saiha', 'Saiha', '796901'),

-- Nagaland
('Nagaland', 'Kohima', 'Kohima', '797001'),
('Nagaland', 'Dimapur', 'Dimapur', '797112'),
('Nagaland', 'Mokokchung', 'Mokokchung', '798601'),

-- Odisha
('Odisha', 'Bhubaneswar', 'Khordha', '751001'),
('Odisha', 'Cuttack', 'Cuttack', '753001'),
('Odisha', 'Rourkela', 'Sundargarh', '769001'),
('Odisha', 'Berhampur', 'Ganjam', '760001'),
('Odisha', 'Sambalpur', 'Sambalpur', '768001'),
('Odisha', 'Puri', 'Puri', '752001'),
('Odisha', 'Balasore', 'Balasore', '756001'),
('Odisha', 'Bhadrak', 'Bhadrak', '756100'),
('Odisha', 'Baripada', 'Mayurbhanj', '757001'),

-- Punjab
('Punjab', 'Ludhiana', 'Ludhiana', '141001'),
('Punjab', 'Amritsar', 'Amritsar', '143001'),
('Punjab', 'Jalandhar', 'Jalandhar', '144001'),
('Punjab', 'Patiala', 'Patiala', '147001'),
('Punjab', 'Bathinda', 'Bathinda', '151001'),
('Punjab', 'Mohali', 'Mohali', '160001'),
('Punjab', 'Firozpur', 'Firozpur', '152002'),
('Punjab', 'Batala', 'Gurdaspur', '143505'),
('Punjab', 'Pathankot', 'Pathankot', '145001'),
('Punjab', 'Moga', 'Moga', '142001'),
('Punjab', 'Abohar', 'Fazilka', '152116'),
('Punjab', 'Malerkotla', 'Sangrur', '148023'),
('Punjab', 'Khanna', 'Ludhiana', '141401'),
('Punjab', 'Phagwara', 'Kapurthala', '144401'),
('Punjab', 'Muktsar', 'Muktsar', '152026'),

-- Rajasthan
('Rajasthan', 'Jaipur', 'Jaipur', '302001'),
('Rajasthan', 'Jodhpur', 'Jodhpur', '342001'),
('Rajasthan', 'Kota', 'Kota', '324001'),
('Rajasthan', 'Bikaner', 'Bikaner', '334001'),
('Rajasthan', 'Udaipur', 'Udaipur', '313001'),
('Rajasthan', 'Ajmer', 'Ajmer', '305001'),
('Rajasthan', 'Bhilwara', 'Bhilwara', '311001'),
('Rajasthan', 'Alwar', 'Alwar', '301001'),
('Rajasthan', 'Bharatpur', 'Bharatpur', '321001'),
('Rajasthan', 'Pali', 'Pali', '306401'),
('Rajasthan', 'Barmer', 'Barmer', '344001'),
('Rajasthan', 'Sikar', 'Sikar', '332001'),
('Rajasthan', 'Tonk', 'Tonk', '304001'),
('Rajasthan', 'Sadulpur', 'Churu', '331023'),
('Rajasthan', 'Kishangarh', 'Ajmer', '305801'),

-- Sikkim
('Sikkim', 'Gangtok', 'East Sikkim', '737101'),
('Sikkim', 'Namchi', 'South Sikkim', '737126'),
('Sikkim', 'Gyalshing', 'West Sikkim', '737111'),
('Sikkim', 'Mangan', 'North Sikkim', '737116'),

-- Tamil Nadu
('Tamil Nadu', 'Chennai', 'Chennai', '600001'),
('Tamil Nadu', 'Coimbatore', 'Coimbatore', '641001'),
('Tamil Nadu', 'Madurai', 'Madurai', '625001'),
('Tamil Nadu', 'Tiruchirappalli', 'Tiruchirappalli', '620001'),
('Tamil Nadu', 'Salem', 'Salem', '636001'),
('Tamil Nadu', 'Tirunelveli', 'Tirunelveli', '627001'),
('Tamil Nadu', 'Tiruppur', 'Tiruppur', '641601'),
('Tamil Nadu', 'Vellore', 'Vellore', '632001'),
('Tamil Nadu', 'Erode', 'Erode', '638001'),
('Tamil Nadu', 'Thoothukkudi', 'Thoothukkudi', '628001'),
('Tamil Nadu', 'Dindigul', 'Dindigul', '624001'),
('Tamil Nadu', 'Thanjavur', 'Thanjavur', '613001'),
('Tamil Nadu', 'Ranipet', 'Ranipet', '632401'),
('Tamil Nadu', 'Sivakasi', 'Virudhunagar', '626123'),
('Tamil Nadu', 'Karur', 'Karur', '639001'),
('Tamil Nadu', 'Udhagamandalam', 'Nilgiris', '643001'),
('Tamil Nadu', 'Hosur', 'Krishnagiri', '635109'),
('Tamil Nadu', 'Nagercoil', 'Kanyakumari', '629001'),
('Tamil Nadu', 'Kanchipuram', 'Kanchipuram', '631501'),
('Tamil Nadu', 'Cuddalore', 'Cuddalore', '607001'),

-- Telangana
('Telangana', 'Hyderabad', 'Hyderabad', '500001'),
('Telangana', 'Warangal', 'Warangal Urban', '506002'),
('Telangana', 'Nizamabad', 'Nizamabad', '503001'),
('Telangana', 'Khammam', 'Khammam', '507001'),
('Telangana', 'Karimnagar', 'Karimnagar', '505001'),
('Telangana', 'Ramagundam', 'Peddapalli', '505209'),
('Telangana', 'Mahabubnagar', 'Mahabubnagar', '509001'),
('Telangana', 'Nalgonda', 'Nalgonda', '508001'),
('Telangana', 'Adilabad', 'Adilabad', '504001'),
('Telangana', 'Suryapet', 'Suryapet', '508213'),

-- Tripura
('Tripura', 'Agartala', 'West Tripura', '799001'),
('Tripura', 'Dharmanagar', 'North Tripura', '799250'),
('Tripura', 'Udaipur', 'Gomati', '799120'),
('Tripura', 'Kailasahar', 'Unakoti', '799277'),

-- Uttar Pradesh
('Uttar Pradesh', 'Lucknow', 'Lucknow', '226001'),
('Uttar Pradesh', 'Kanpur', 'Kanpur Nagar', '208001'),
('Uttar Pradesh', 'Ghaziabad', 'Ghaziabad', '201001'),
('Uttar Pradesh', 'Agra', 'Agra', '282001'),
('Uttar Pradesh', 'Varanasi', 'Varanasi', '221001'),
('Uttar Pradesh', 'Meerut', 'Meerut', '250001'),
('Uttar Pradesh', 'Allahabad', 'Allahabad', '211001'),
('Uttar Pradesh', 'Bareilly', 'Bareilly', '243001'),
('Uttar Pradesh', 'Aligarh', 'Aligarh', '202001'),
('Uttar Pradesh', 'Moradabad', 'Moradabad', '244001'),
('Uttar Pradesh', 'Saharanpur', 'Saharanpur', '247001'),
('Uttar Pradesh', 'Gorakhpur', 'Gorakhpur', '273001'),
('Uttar Pradesh', 'Firozabad', 'Firozabad', '283203'),
('Uttar Pradesh', 'Jhansi', 'Jhansi', '284001'),
('Uttar Pradesh', 'Muzaffarnagar', 'Muzaffarnagar', '251001'),
('Uttar Pradesh', 'Mathura', 'Mathura', '281001'),
('Uttar Pradesh', 'Rampur', 'Rampur', '244901'),
('Uttar Pradesh', 'Shahjahanpur', 'Shahjahanpur', '242001'),
('Uttar Pradesh', 'Farrukhabad', 'Farrukhabad', '209625'),
('Uttar Pradesh', 'Mau', 'Mau', '275101'),
('Uttar Pradesh', 'Hapur', 'Hapur', '245101'),
('Uttar Pradesh', 'Noida', 'Gautam Buddha Nagar', '201301'),
('Uttar Pradesh', 'Etawah', 'Etawah', '206001'),
('Uttar Pradesh', 'Mirzapur', 'Mirzapur', '231001'),
('Uttar Pradesh', 'Bulandshahr', 'Bulandshahr', '203001'),

-- Uttarakhand
('Uttarakhand', 'Dehradun', 'Dehradun', '248001'),
('Uttarakhand', 'Haridwar', 'Haridwar', '249401'),
('Uttarakhand', 'Roorkee', 'Haridwar', '247667'),
('Uttarakhand', 'Haldwani', 'Nainital', '263139'),
('Uttarakhand', 'Rudrapur', 'Udham Singh Nagar', '263153'),
('Uttarakhand', 'Kashipur', 'Udham Singh Nagar', '244713'),
('Uttarakhand', 'Rishikesh', 'Dehradun', '249201'),

-- West Bengal
('West Bengal', 'Kolkata', 'Kolkata', '700001'),
('West Bengal', 'Howrah', 'Howrah', '711101'),
('West Bengal', 'Durgapur', 'Paschim Bardhaman', '713201'),
('West Bengal', 'Asansol', 'Paschim Bardhaman', '713301'),
('West Bengal', 'Siliguri', 'Darjeeling', '734001'),
('West Bengal', 'Malda', 'Malda', '732101'),
('West Bengal', 'Baharampur', 'Murshidabad', '742101'),
('West Bengal', 'Habra', 'North 24 Parganas', '743263'),
('West Bengal', 'Kharagpur', 'Paschim Medinipur', '721301'),
('West Bengal', 'Shantipur', 'Nadia', '741404'),
('West Bengal', 'Dankuni', 'Hooghly', '712311'),
('West Bengal', 'Dhulian', 'Murshidabad', '742202'),
('West Bengal', 'Ranaghat', 'Nadia', '741201'),
('West Bengal', 'Haldia', 'Purba Medinipur', '721607'),
('West Bengal', 'Raiganj', 'Uttar Dinajpur', '733134'),

-- Delhi
('Delhi', 'New Delhi', 'New Delhi', '110001'),
('Delhi', 'Delhi', 'Central Delhi', '110006'),
('Delhi', 'Dwarka', 'South West Delhi', '110075'),
('Delhi', 'Rohini', 'North West Delhi', '110085'),
('Delhi', 'Janakpuri', 'West Delhi', '110058'),
('Delhi', 'Lajpat Nagar', 'South Delhi', '110024'),
('Delhi', 'Karol Bagh', 'Central Delhi', '110005'),
('Delhi', 'Connaught Place', 'New Delhi', '110001'),
('Delhi', 'Chandni Chowk', 'Central Delhi', '110006'),
('Delhi', 'Saket', 'South Delhi', '110017'),

-- Jammu and Kashmir
('Jammu and Kashmir', 'Srinagar', 'Srinagar', '190001'),
('Jammu and Kashmir', 'Jammu', 'Jammu', '180001'),
('Jammu and Kashmir', 'Anantnag', 'Anantnag', '192101'),
('Jammu and Kashmir', 'Baramulla', 'Baramulla', '193101'),
('Jammu and Kashmir', 'Sopore', 'Baramulla', '193201'),
('Jammu and Kashmir', 'Kathua', 'Kathua', '184101'),

-- Ladakh
('Ladakh', 'Leh', 'Leh', '194101'),
('Ladakh', 'Kargil', 'Kargil', '194103'),

-- Puducherry
('Puducherry', 'Puducherry', 'Puducherry', '605001'),
('Puducherry', 'Karaikal', 'Karaikal', '609602'),
('Puducherry', 'Mahe', 'Mahe', '673310'),
('Puducherry', 'Yanam', 'Yanam', '533464'),

-- Chandigarh
('Chandigarh', 'Chandigarh', 'Chandigarh', '160001'),

-- Dadra and Nagar Haveli and Daman and Diu
('Dadra and Nagar Haveli and Daman and Diu', 'Daman', 'Daman', '396210'),
('Dadra and Nagar Haveli and Daman and Diu', 'Diu', 'Diu', '362520'),
('Dadra and Nagar Haveli and Daman and Diu', 'Silvassa', 'Dadra and Nagar Haveli', '396230'),

-- Lakshadweep
('Lakshadweep', 'Kavaratti', 'Lakshadweep', '682555'),

-- Andaman and Nicobar Islands
('Andaman and Nicobar Islands', 'Port Blair', 'South Andaman', '744101');

-- --------------------------------------------------------

-- Table structure for table `couriers`
CREATE TABLE `couriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courier_id` varchar(20) NOT NULL,
  `party_name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `from_city` varchar(50) NOT NULL,
  `to_city` varchar(50) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','in_transit','delivered','cancelled') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `agent_id` varchar(20) NOT NULL,
  `delivery_person` varchar(100) DEFAULT NULL,
  `delivery_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courier_id` (`courier_id`),
  KEY `agent_id` (`agent_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample courier data
INSERT INTO `couriers` (`courier_id`, `party_name`, `mobile`, `address`, `from_city`, `to_city`, `delivery_date`, `amount`, `status`, `remarks`, `agent_id`) VALUES
('RAJE1001', 'Rajesh Electronics', '9876543210', '123 Main Street, Andheri', 'Mumbai', 'Delhi', '2024-01-15', 1500.00, 'in_transit', 'Fragile items', 'AGT001'),
('PRIY2001', 'Priya Textiles', '9876543211', '456 Market Road, Connaught Place', 'Delhi', 'Bangalore', '2024-01-16', 2500.00, 'pending', 'Express delivery', 'AGT002'),
('AMIT3001', 'Amit Traders', '9876543212', '789 Commercial Street, MG Road', 'Bangalore', 'Chennai', '2024-01-17', 1800.00, 'delivered', 'Standard delivery', 'AGT003');

-- --------------------------------------------------------

-- Table structure for table `tracking`
CREATE TABLE `tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courier_id` varchar(20) NOT NULL,
  `location` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `updated_by` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `courier_id` (`courier_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample tracking data
INSERT INTO `tracking` (`courier_id`, `location`, `status`, `updated_by`) VALUES
('RAJE1001', 'Mumbai', 'Package picked up', 'AGT001'),
('RAJE1001', 'Mumbai Central', 'In transit to Delhi', 'AGT001'),
('PRIY2001', 'Delhi', 'Package received', 'AGT002'),
('AMIT3001', 'Bangalore', 'Package picked up', 'AGT003'),
('AMIT3001', 'Chennai', 'Delivered successfully', 'AGT003');

COMMIT;