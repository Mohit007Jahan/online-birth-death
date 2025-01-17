// Bangladesh location data - Updated with all divisions
const bangladeshData = {
    'Khulna': {
        districts: {
            'Khulna': {
                upazilas: {
                    'Khulna City': ['Khulna City Corporation', 'Sonadanga', 'Khalishpur', 'Daulatpur', 'Boyra', 'Tutpara', 'Rupsha'],
                    'Dumuria': ['Dumuria Pouroshova', 'Rudaghara', 'Maguraghona', 'Sahos', 'Gutudia', 'Atlia', 'Dhamalia'],
                    'Batiaghata': ['Batiaghata Pouroshova', 'Jalma', 'Gangarampur', 'Surkhali', 'Amirpur', 'Baliadanga', 'Kismatfultola'],
                    'Dacope': ['Dacope Pouroshova', 'Chalna', 'Bajua', 'Banishanta', 'Kamarkhola', 'Laudoba', 'Sutarkhali'],
                    'Phultala': ['Phultala Pouroshova', 'Damodar', 'Jamira', 'Phultala', 'Senhati', 'Arua', 'Krishnanagar'],
                    'Terokhada': ['Terokhada Pouroshova', 'Sachiadah', 'Barakpur', 'Ajgara', 'Modhupur', 'Shalua', 'Terokhada'],
                    'Dighalia': ['Dighalia Pouroshova', 'Gazirhat', 'Senhati', 'Barasat', 'Dighalia', 'Jugipole'],
                    'Rupsa': ['Rupsa Pouroshova', 'Naihati', 'Ghatbhogh', 'Tilok', 'Rupsa', 'Sharafpur'],
                    'Koyra': ['Koyra Pouroshova', 'Koyra', 'Maharajpur', 'Maheshwaripur', 'North Bedkashi', 'South Bedkashi']
                }
            },
            'Bagerhat': {
                upazilas: {
                    'Bagerhat Sadar': ['Bagerhat Pouroshova', 'Dema', 'Khanpur', 'Jatrapur', 'Baruipara', 'Karapara', 'Rakhalgachi'],
                    'Mongla': ['Mongla Pouroshova', 'Chila', 'Burirdanga', 'Mithakhali', 'Chandpai', 'Sonailtala', 'Sundarban'],
                    'Morrelganj': ['Morrelganj Pouroshova', 'Baharbunia', 'Hoglapasha', 'Teligati', 'Khaulia', 'Nishanbaria', 'Panchakaran'],
                    'Rampal': ['Rampal Pouroshova', 'Baintala', 'Perikhali', 'Rajnagar', 'Hurka', 'Ujalkur', 'Mollikerber'],
                    'Fakirhat': ['Fakirhat Pouroshova', 'Bahirdia', 'Betaga', 'Mulghar', 'Naldha', 'Subhadia'],
                    'Kachua': ['Kachua Pouroshova', 'Gopalpur', 'Kachua', 'Maghia', 'Rari', 'Udaypur'],
                    'Chitalmari': ['Chitalmari Pouroshova', 'Chitalmari', 'Hizla', 'Kalatala', 'Shibpur', 'Santoshpur'],
                    'Mollahat': ['Mollahat Pouroshova', 'Chunkhola', 'Gangni', 'Kulia', 'Gaola', 'Udaypur'],
                    'Sarankhola': ['Sarankhola Pouroshova', 'Dhansagar', 'Khontakata', 'Rayenda', 'Southkhali']
                }
            },
            'Satkhira': {
                upazilas: {
                    'Satkhira Sadar': ['Satkhira Pouroshova', 'Alipur', 'Brahmarajpur', 'Fingri', 'Labsa', 'Ghona', 'Baikari'],
                    'Kalaroa': ['Kalaroa Pouroshova', 'Chandanpur', 'Jogikhali', 'Helatala', 'Keragachhi', 'Langaljhara', 'Sonabaria'],
                    'Tala': ['Tala Pouroshova', 'Islamkati', 'Khalilnagar', 'Khalishkhali', 'Kumira', 'Sarulia', 'Tentulia'],
                    'Kaliganj': ['Kaliganj Pouroshova', 'Bishnupur', 'Champaphul', 'Krishnanagar', 'Mathureshpur', 'Nalta', 'Tarali'],
                    'Shyamnagar': ['Shyamnagar Pouroshova', 'Gabura', 'Burigoalini', 'Munshiganj', 'Kashimari', 'Ramjannagar', 'Shyamnagar'],
                    'Assasuni': ['Assasuni Pouroshova', 'Assasuni', 'Budhhata', 'Khajra', 'Pratapnagar', 'Sreeula'],
                    'Debhata': ['Debhata Pouroshova', 'Debhata', 'Kulia', 'Noapara', 'Parulia', 'Sakhipur']
                }
            },
            'Jessore': {
                upazilas: {
                    'Jessore Sadar': ['Jessore Pouroshova', 'Arabpur', 'Basundia', 'Chanchra', 'Churamankati', 'Fathehpur', 'Gadkhali'],
                    'Abhaynagar': ['Abhaynagar Pouroshova', 'Baghutia', 'Chalishia', 'Noapara', 'Prembag', 'Siddhipasha', 'Sundali'],
                    'Bagherpara': ['Bagherpara Pouroshova', 'Bagherpara', 'Dhalgram', 'Jaharpur', 'Narikelbaria', 'Roypur', 'Sundali'],
                    'Chaugachha': ['Chaugachha Pouroshova', 'Chaugachha', 'Dhuliani', 'Hakimpur', 'Jagannathpur', 'Pashapole', 'Sukpukhuria'],
                    'Jhikargachha': ['Jhikargachha Pouroshova', 'Bankra', 'Gadkhali', 'Jhikargachha', 'Nabharan', 'Panisara', 'Shankarpur'],
                    'Keshabpur': ['Keshabpur Pouroshova', 'Bidyanandakati', 'Gaurighona', 'Keshabpur', 'Majidpur', 'Panjia', 'Sagardari'],
                    'Manirampur': ['Manirampur Pouroshova', 'Bhojgati', 'Chaluahati', 'Dhakuria', 'Jhanpa', 'Kultia', 'Manirampur'],
                    'Sharsha': ['Sharsha Pouroshova', 'Bagachra', 'Benapole', 'Dihi', 'Goga', 'Putkhali', 'Sharsha']
                }
            },
            'Magura': {
                upazilas: {
                    'Magura Sadar': ['Magura Pouroshova', 'Baroilpur', 'Hazrapur', 'Kuchiamora', 'Raghobdair', 'Satrijitpur'],
                    'Mohammadpur': ['Mohammadpur Pouroshova', 'Balidia', 'Binodpur', 'Mohammadpur', 'Rajapur', 'Satrujitpur'],
                    'Shalikha': ['Shalikha Pouroshova', 'Arpara', 'Bunagati', 'Dhaneswargati', 'Shalikha', 'Talkhari'],
                    'Sreepur': ['Sreepur Pouroshova', 'Atharokhada', 'Dariapur', 'Nakol', 'Sreepur', 'Talkhari']
                }
            },
            'Narail': {
                upazilas: {
                    'Narail Sadar': ['Narail Pouroshova', 'Auria', 'Banshgram', 'Bichhali', 'Chandiborpur', 'Singasolpur'],
                    'Lohagara': ['Lohagara Pouroshova', 'Dighalia', 'Itna', 'Kashipur', 'Lahuria', 'Naldi'],
                    'Kalia': ['Kalia Pouroshova', 'Babra Hachla', 'Hamidpur', 'Kalabaria', 'Maijpara', 'Purulia']
                }
            },
            'Jhenaidah': {
                upazilas: {
                    'Jhenaidah Sadar': ['Jhenaidah Pouroshova', 'Dogachhi', 'Furshondi', 'Ghorshal', 'Hajipur', 'Saganna'],
                    'Maheshpur': ['Maheshpur Pouroshova', 'Fatehpur', 'Jadabpur', 'Kazirber', 'Maheshpur', 'Natima'],
                    'Kaliganj': ['Kaliganj Pouroshova', 'Bara Bazar', 'Kaliganj', 'Naldanga', 'Rakhalgachhi', 'Tribeni'],
                    'Kotchandpur': ['Kotchandpur Pouroshova', 'Baluhar', 'Elangi', 'Kotchandpur', 'Kushna', 'Sabdalpur'],
                    'Shailkupa': ['Shailkupa Pouroshova', 'Abaipur', 'Hakimpur', 'Shailkupa', 'Sundarpura', 'Tribeni'],
                    'Harinakunda': ['Harinakunda Pouroshova', 'Chandpur', 'Harinakunda', 'Joradah', 'Padmakar', 'Surat']
                }
            },
            'Chuadanga': {
                upazilas: {
                    'Chuadanga Sadar': ['Chuadanga Pouroshova', 'Alukdia', 'Begumpur', 'Kutubpur', 'Mominpur', 'Padmabila'],
                    'Alamdanga': ['Alamdanga Pouroshova', 'Ailhash', 'Damurhuda', 'Jehala', 'Juranpur', 'Khadimpur'],
                    'Damurhuda': ['Damurhuda Pouroshova', 'Darshana', 'Juranpur', 'Kapashdanga', 'Natipota', 'Uzangram'],
                    'Jibannagar': ['Jibannagar Pouroshova', 'Andulbaria', 'Baradi', 'Gangni', 'Jehala', 'Simanta']
                }
            },
            'Kushtia': {
                upazilas: {
                    'Kushtia Sadar': ['Kushtia Pouroshova', 'Ailchara', 'Barkhada', 'Hatas', 'Majampur', 'Panti'],
                    'Kumarkhali': ['Kumarkhali Pouroshova', 'Chandpur', 'Jagannathpur', 'Panti', 'Sadaki', 'Shelaidah'],
                    'Khoksa': ['Khoksa Pouroshova', 'Ambaria', 'Betbaria', 'Gopgram', 'Janipur', 'Osmanpur'],
                    'Mirpur': ['Mirpur Pouroshova', 'Ambaria', 'Bahadurpur', 'Chhatian', 'Poradaha', 'Talbaria'],
                    'Bheramara': ['Bheramara Pouroshova', 'Bahirchar', 'Dharampur', 'Juniadah', 'Mokarimpur', 'Sadipur'],
                    'Daulatpur': ['Daulatpur Pouroshova', 'Adabaria', 'Chilmari', 'Maricha', 'Philipnagar', 'Ramkrishnapur']
                }
            },
            'Meherpur': {
                upazilas: {
                    'Meherpur Sadar': ['Meherpur Pouroshova', 'Amjhupi', 'Amjhupi', 'Buripota', 'Kutubpur', 'Pirojpur'],
                    'Gangni': ['Gangni Pouroshova', 'Bamundi', 'Gangni', 'Kathuli', 'Shaharbati', 'Tentulbaria'],
                    'Mujibnagar': ['Mujibnagar Pouroshova', 'Dariapur', 'Joypur', 'Mujibnagar', 'Monakhali', 'Nityanandapur']
                }
            }
        }
    }
}; 