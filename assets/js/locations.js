// Bangladesh location data - Updated with Mymensingh Division
const bangladeshData = {
    'Dhaka': {
        districts: {
            'Dhaka': {
                upazilas: {
                    'Dhaka City': ['Dhaka North City Corporation', 'Dhaka South City Corporation'],
                    'Savar': ['Savar Pouroshova', 'Ashulia', 'Birulia', 'Yearpur', 'Dhamsona', 'Kaundia', 'Tetuljhora', 'Pathalia'],
                    'Dhamrai': ['Dhamrai Pouroshova', 'Kalampur', 'Amta', 'Nannar', 'Kulla', 'Gangutia', 'Sombhag'],
                    'Keraniganj': ['Keraniganj Pouroshova', 'Hazratpur', 'Kalatia', 'Zinjira', 'Aganagar', 'Shuvadda', 'Rohitpur', 'Basta'],
                    'Dohar': ['Dohar Pouroshova', 'Kushumhati', 'Nayabari', 'Moksudpur', 'Bilashpur', 'Kaundia', 'Tetuljhora', 'Patharail'],
                    'Nawabganj': ['Nawabganj Pouroshova', 'Bakshnagar', 'Churain', 'Kalakopa', 'Agla', 'Jantrail', 'Kolakopa']
                }
            },
            'Gazipur': {
                upazilas: {
                    'Gazipur Sadar': ['Gazipur City Corporation', 'Kashimpur', 'Konabari', 'Tongi', 'Pubail', 'Kayaltia', 'Baria'],
                    'Kapasia': ['Kapasia Pouroshova', 'Toke', 'Chandpur', 'Rayed', 'Barishabo', 'Targaon', 'Durgapur'],
                    'Kaliganj': ['Kaliganj Pouroshova', 'Baktarpur', 'Jangalia', 'Moktarpur', 'Nagari', 'Santanpur', 'Tumlia'],
                    'Sreepur': ['Sreepur Pouroshova', 'Telihati', 'Gosinga', 'Barmi', 'Kawraid', 'Maona', 'Prahalladpur'],
                    'Kaliakair': ['Kaliakair Pouroshova', 'Mouchak', 'Sutrapur', 'Fulbaria', 'Madhyapara', 'Sreefaltali', 'Atabaha']
                }
            },
            'Narayanganj': {
                upazilas: {
                    'Narayanganj Sadar': ['Narayanganj City Corporation', 'Fatulla', 'Siddhirganj', 'Kadam Rasul', 'Enayetnagar', 'Kutubpur', 'Alirtek'],
                    'Sonargaon': ['Sonargaon Pouroshova', 'Jampur', 'Pirojpur', 'Mograpara', 'Baidyer Bazar', 'Shambhupura', 'Dulalpur'],
                    'Rupganj': ['Rupganj Pouroshova', 'Murapara', 'Bhulta', 'Golakandail', 'Daudpur', 'Kayetpara', 'Bholabo'],
                    'Araihazar': ['Araihazar Pouroshova', 'Satgram', 'Duptara', 'Gopaldi', 'Bishnandi', 'Mahmudpur', 'Khagkanda'],
                    'Bandar': ['Bandar Pouroshova', 'Kadam Rasul', 'Madanganj', 'BIDS', 'Musapur', 'Dhamgar', 'Kolagathia']
                }
            },
            'Narsingdi': {
                upazilas: {
                    'Narsingdi Sadar': ['Narsingdi Pouroshova', 'Madhabdi', 'Panchdona', 'UttarBakharnagar', 'Chinishpur', 'Silmandi', 'Meherpara'],
                    'Palash': ['Palash Pouroshova', 'Ghorashal', 'Char Sindur', 'Danga', 'Gazaria', 'Jinardi', 'Char Dighaldi'],
                    'Shibpur': ['Shibpur Pouroshova', 'Masimpur', 'Joynagar', 'Baghabo', 'Dulalpur', 'Putia', 'Sadharchar'],
                    'Belabo': ['Belabo Pouroshova', 'Nilkuthi', 'Patuli', 'Narayanpur', 'Sallabad', 'Bajnabo', 'Chalakchar'],
                    'Monohardi': ['Monohardi Pouroshova', 'Katabaria', 'Chandanbari', 'Gotashia', 'Ekduaria', 'Kanchikata', 'Charmandalia'],
                    'Raipura': ['Raipura Pouroshova', 'Amirganj', 'Banshgari', 'Chanderkandi', 'Hairmara', 'Maheshpur', 'Mirzanagar']
                }
            },
            'Tangail': {
                upazilas: {
                    'Tangail Sadar': ['Tangail Pouroshova', 'Santosh', 'Porabari', 'Gharinda', 'Katuli', 'Baghil', 'Kakrajan'],
                    'Kalihati': ['Kalihati Pouroshova', 'Elenga', 'Bangra', 'Paikara', 'Durgapur', 'Nagbari', 'Shahadebpur'],
                    'Nagarpur': ['Nagarpur Pouroshova', 'Salimabad', 'Duptiair', 'Bekra', 'Mamudnagar', 'Mokna', 'Pakutia'],
                    'Basail': ['Basail Pouroshova', 'Kanchanpur', 'Kashil', 'Fulki', 'Habla', 'Kauljani'],
                    'Madhupur': ['Madhupur Pouroshova', 'Auronkhola', 'Dhobari', 'Kakraid', 'Mirzapur', 'Sholakuri'],
                    'Ghatail': ['Ghatail Pouroshova', 'Dhalapara', 'Digar', 'Jamuria', 'Lokerpara', 'Rasulpur'],
                    'Gopalpur': ['Gopalpur Pouroshova', 'Hemnagar', 'Jhawail', 'Mirzapur', 'Nagdashimla', 'Salandar'],
                    'Delduar': ['Delduar Pouroshova', 'Atia', 'Deuli', 'Elasin', 'Fazilhati', 'Patharail'],
                    'Bhuapur': ['Bhuapur Pouroshova', 'Arjuna', 'Falda', 'Gabsara', 'Gobindasi', 'Nikrail'],
                    'Mirzapur': ['Mirzapur Pouroshova', 'Banail', 'Bahuria', 'Fatejangpur', 'Gorai', 'Tarafpur'],
                    'Sakhipur': ['Sakhipur Pouroshova', 'Bahera', 'Dariapur', 'Gala', 'Jadunathpur', 'Kalmegha'],
                    'Dhanbari': ['Dhanbari Pouroshova', 'Baniajan', 'Birtara', 'Dhopakhali', 'Gohaliabari', 'Rajnarayanpur']
                }
            },
            'Kishoreganj': {
                upazilas: {
                    'Kishoreganj Sadar': ['Kishoreganj Pouroshova', 'Latibabad', 'Maizkhapan', 'Nandail', 'Rashidabad', 'Baulai', 'Chouganga'],
                    'Bhairab': ['Bhairab Pouroshova', 'Shimulkandi', 'Sadekpur', 'Gazaria', 'Kalika Prasad', 'Shibpur', 'Zilukar'],
                    'Bajitpur': ['Bajitpur Pouroshova', 'Laksmipur', 'Marichkhali', 'Sararchar', 'Dilalpur', 'Pirijpur', 'Sahedal'],
                    'Kuliarchar': ['Kuliarchar Pouroshova', 'Chhoysuti', 'Joyka', 'Faridpur', 'Osmanpur', 'Ramdi'],
                    'Karimganj': ['Karimganj Pouroshova', 'Gujadia', 'Jafarabad', 'Noabad', 'Sukhia', 'Gundhar'],
                    'Tarail': ['Tarail Pouroshova', 'Dhala', 'Jawar', 'Rauti', 'Talganga', 'Banagram'],
                    'Hossainpur': ['Hossainpur Pouroshova', 'Gobindapur', 'Jinari', 'Pumdi', 'Sahedal', 'Sidhla'],
                    'Pakundia': ['Pakundia Pouroshova', 'Chandipasha', 'Charfaradi', 'Egarasindur', 'Hosendi', 'Sukhia'],
                    'Katiadi': ['Katiadi Pouroshova', 'Banagram', 'Chandpur', 'Jangalia', 'Masua', 'Shahasram'],
                    'Mithamain': ['Mithamain Pouroshova', 'Bairati', 'Dhaki', 'Ghagra', 'Kewarjore', 'Mithamain'],
                    'Nikli': ['Nikli Pouroshova', 'Dampara', 'Jaraitala', 'Karpasha', 'Muzaffer Char', 'Singpur'],
                    'Austagram': ['Austagram Pouroshova', 'Bangalpara', 'Kalma', 'Kastul', 'Purba Austagram', 'Paschim Austagram']
                }
            },
            'Faridpur': {
                upazilas: {
                    'Faridpur Sadar': ['Faridpur Pouroshova', 'Aliabad', 'Kanaipur', 'Decreer Char', 'Gerda', 'Ishan Gopalpur', 'Krishnanagar'],
                    'Boalmari': ['Boalmari Pouroshova', 'Gunbaha', 'Moyna', 'Chandpur', 'Ghoshpur', 'Rupapat', 'Satair'],
                    'Madhukhali': ['Madhukhali Pouroshova', 'Jahapur', 'Kamarkhali', 'Bagat', 'Dumain', 'Gajna', 'Megchami'],
                    'Alfadanga': ['Alfadanga Pouroshova', 'Bana', 'Gopalpur', 'Pachuria', 'Tagarbanda'],
                    'Bhanga': ['Bhanga Pouroshova', 'Nurullagonj', 'Nasirabad', 'Hamirdi', 'Kawlibera', 'Tujarpur'],
                    'Charbhadrasan': ['Charbhadrasan Pouroshova', 'Char Harirampur', 'Char Jahukanda', 'Gazirtek'],
                    'Nagarkanda': ['Nagarkanda Pouroshova', 'Talma', 'Raipur', 'Kaichail', 'Manitara', 'Phulsuti'],
                    'Sadarpur': ['Sadarpur Pouroshova', 'Char Manair', 'Dheukhali', 'Krishnapur', 'Narikelbaria']
                }
            },
            'Gopalganj': {
                upazilas: {
                    'Gopalganj Sadar': ['Gopalganj Pouroshova', 'Ulpur', 'Borashi', 'Dignagar', 'Gobra', 'Karpara', 'Kusli'],
                    'Tungipara': ['Tungipara Pouroshova', 'Patgati', 'Gopalpur', 'Kushli', 'Dumaria', 'Raghdi'],
                    'Kotalipara': ['Kotalipara Pouroshova', 'Raghdi', 'Kushla', 'Amtoli', 'Pinjuri', 'Ramshil'],
                    'Kashiani': ['Kashiani Pouroshova', 'Bethuri', 'Fukura', 'Maheshpur', 'Nijamkandi', 'Ratail'],
                    'Muksudpur': ['Muksudpur Pouroshova', 'Gopinathpur', 'Kajulia', 'Khandarpara', 'Raghunathpur', 'Ujani']
                }
            },
            'Madaripur': {
                upazilas: {
                    'Madaripur Sadar': ['Madaripur Pouroshova', 'Chiler Char', 'Dhurail', 'Kendua', 'Kunia', 'Mustafapur'],
                    'Kalkini': ['Kalkini Pouroshova', 'Amgram', 'Baligram', 'Dashar', 'Enayetnagar', 'Sahebrampur'],
                    'Rajoir': ['Rajoir Pouroshova', 'Badarpasha', 'Hosenpur', 'Islampur', 'Kadambari', 'Rajoir'],
                    'Shibchar': ['Shibchar Pouroshova', 'Bandarkhola', 'Bhadrasion', 'Charjanajat', 'Ditiyakhando', 'Umedpur']
                }
            },
            'Manikganj': {
                upazilas: {
                    'Manikganj Sadar': ['Manikganj Pouroshova', 'Betila', 'Bhararia', 'Garpara', 'Jagir', 'Putail'],
                    'Singair': ['Singair Pouroshova', 'Baldhara', 'Chandhar', 'Jamsha', 'Joymantap', 'Talibpur'],
                    'Shibalaya': ['Shibalaya Pouroshova', 'Arua', 'Mohadebpur', 'Shibalaya', 'Teota', 'Utholi'],
                    'Saturia': ['Saturia Pouroshova', 'Baraid', 'Dhankora', 'Fukurhati', 'Hargaj', 'Tilli'],
                    'Harirampur': ['Harirampur Pouroshova', 'Balara', 'Boyra', 'Dhulsura', 'Gior', 'Kanchanpur'],
                    'Ghior': ['Ghior Pouroshova', 'Baratia', 'Baniajuri', 'Ghior', 'Nali', 'Paila'],
                    'Daulatpur': ['Daulatpur Pouroshova', 'Bachamara', 'Chala', 'Jionpur', 'Kalia', 'Tilli']
                }
            },
            'Munshiganj': {
                upazilas: {
                    'Munshiganj Sadar': ['Munshiganj Pouroshova', 'Adhara', 'Bajrajogini', 'Birtara', 'Kumarbhog', 'Panchasar'],
                    'Gazaria': ['Gazaria Pouroshova', 'Baluakandi', 'Bhaber Char', 'Guagachia', 'Hosendi', 'Tengarchar'],
                    'Lohajang': ['Lohajang Pouroshova', 'Baultoli', 'Gaodia', 'Haldia', 'Kalma', 'Teotia'],
                    'Sirajdikhan': ['Sirajdikhan Pouroshova', 'Bashail', 'Birtara', 'Keyain', 'Madhypara', 'Rarikhal'],
                    'Sreenagar': ['Sreenagar Pouroshova', 'Baghra', 'Bhagyakul', 'Hashara', 'Kolapara', 'Patabhog'],
                    'Tongibari': ['Tongibari Pouroshova', 'Autshahi', 'Betka', 'Dighirpar', 'Kamarkhara', 'Sonarong']
                }
            },
            'Rajbari': {
                upazilas: {
                    'Rajbari Sadar': ['Rajbari Pouroshova', 'Alipur', 'Banibaha', 'Chandani', 'Dadshi', 'Mizanpur'],
                    'Baliakandi': ['Baliakandi Pouroshova', 'Baharpur', 'Islampur', 'Jamalpur', 'Narua', 'Sajail'],
                    'Goalanda': ['Goalanda Pouroshova', 'Chhota Bhakla', 'Daulatdia', 'Debagram', 'Goalanda', 'Uzancar'],
                    'Kalukhali': ['Kalukhali Pouroshova', 'Boalia', 'Kasba', 'Majbari', 'Ratandia', 'Sardanga'],
                    'Pangsha': ['Pangsha Pouroshova', 'Bahadurpur', 'Khankhanapur', 'Mrigibazar', 'Panchuria', 'Sarisha']
                }
            },
            'Shariatpur': {
                upazilas: {
                    'Shariatpur Sadar': ['Shariatpur Pouroshova', 'Angaria', 'Chikandi', 'Domsar', 'Palong', 'Tulasar'],
                    'Bhedarganj': ['Bhedarganj Pouroshova', 'Arshi', 'Charbhaga', 'Dhakhin Tarabunia', 'Gosairhat', 'Mohammadpur'],
                    'Damudya': ['Damudya Pouroshova', 'Darulaman', 'Islampur', 'Sidya', 'Sutalori', 'Tarabunia'],
                    'Gosairhat': ['Gosairhat Pouroshova', 'Gariber Char', 'Idilpur', 'Kodalpur', 'Nager Para', 'Samantasar'],
                    'Naria': ['Naria Pouroshova', 'Bhojeshwar', 'Gharishar', 'Kedarpur', 'Moktarer Char', 'Shulpara'],
                    'Zajira': ['Zajira Pouroshova', 'Barakandi', 'Binodpur', 'Mulna', 'Purba Damudya', 'Zajira']
                }
            }
        }
    },
    'Chittagong': {
        districts: {
            'Chittagong': {
                upazilas: {
                    'Chittagong City': ['Chittagong City Corporation', 'Patenga', 'Pahartali', 'Halishahar', 'Agrabad', 'Khulshi', 'Nasirabad'],
                    'Hathazari': ['Hathazari Pouroshova', 'Fatikchhari', 'Mirsharai', 'Nanupur', 'Chipatali', 'Dharmapur', 'Shikarpur'],
                    'Patiya': ['Patiya Pouroshova', 'Boalkhali', 'Chandanaish', 'Juldha', 'Kachuai', 'Baralia', 'Kusumpura'],
                    'Rangunia': ['Rangunia Pouroshova', 'Raozan', 'Lohagara', 'Padua', 'Chandraghona', 'Kodala', 'Mariamnagar'],
                    'Sitakunda': ['Sitakunda Pouroshova', 'Barabkunda', 'Kumira', 'Bhatiari', 'Banshbaria', 'Muradpur', 'Sonaichhari'],
                    'Anwara': ['Anwara Pouroshova', 'Paraikora', 'Battali', 'Burumchara', 'Chatari', 'Haildhar', 'Roypur'],
                    'Chandanaish': ['Chandanaish Pouroshova', 'Bailtali', 'Barkal', 'Dhopachhari', 'Hashimpur', 'Satkania'],
                    'Fatikchhari': ['Fatikchhari Pouroshova', 'Bhujpur', 'Harualchhari', 'Narayanhat', 'Rangamatia', 'Suabil'],
                    'Lohagara': ['Lohagara Pouroshova', 'Adhunagar', 'Amirabad', 'Chunati', 'Padua', 'Putibila'],
                    'Mirsharai': ['Mirsharai Pouroshova', 'Dhum', 'Jorarganj', 'Katachhara', 'Mithanala', 'Wahedpur'],
                    'Raozan': ['Raozan Pouroshova', 'Bagoan', 'Binajuri', 'Gohira', 'Noajispur', 'Urkirchar'],
                    'Sandwip': ['Sandwip Pouroshova', 'Amanullah', 'Haramia', 'Magdhara', 'Musapur', 'Santoshpur'],
                    'Satkania': ['Satkania Pouroshova', 'Amilais', 'Bazalia', 'Dharmapur', 'Kaliais', 'Sonakania']
                }
            },
            'Cox\'s Bazar': {
                upazilas: {
                    'Cox\'s Bazar Sadar': ['Cox\'s Bazar Pouroshova', 'Jhilongja', 'Islamabad', 'Light House', 'Khurushkul', 'Pokkhali', 'Varuakhali'],
                    'Teknaf': ['Teknaf Pouroshova', 'Sabrang', 'Whykong', 'Hnila', 'Baharchhara', 'Teknaf', 'St. Martin'],
                    'Ukhia': ['Ukhia Pouroshova', 'Ratnapalong', 'Palongkhali', 'Kutupalong', 'Haldia Palong', 'Raja Palong'],
                    'Maheshkhali': ['Maheshkhali Pouroshova', 'Hoanak', 'Matarbari', 'Dhalghata', 'Kalarmarchhara', 'Saflapur'],
                    'Ramu': ['Ramu Pouroshova', 'Khuniapalong', 'Rajarkul', 'Joarianala', 'Dakkin Mithachari', 'Garjania'],
                    'Chakaria': ['Chakaria Pouroshova', 'Baraitali', 'Chiringa', 'Dulahazara', 'Harbang', 'Kayerbil'],
                    'Pekua': ['Pekua Pouroshova', 'Magnama', 'Rajakhali', 'Shilkhali', 'Taitong', 'Ujantia'],
                    'Kutubdia': ['Kutubdia Pouroshova', 'Ali Akbar Deil', 'Boroghop', 'Kaiyarbil', 'Lemsikhali']
                }
            },
            'Rangamati': {
                upazilas: {
                    'Rangamati Sadar': ['Rangamati Pouroshova', 'Vedvedi', 'Kalampati', 'Tabalchhari', 'Baghaichhari', 'Jibtali'],
                    'Kaptai': ['Kaptai Pouroshova', 'Wagga', 'Chandraghona', 'Raikhali', 'Kaptai', 'Karnafuli'],
                    'Kawkhali': ['Kawkhali Pouroshova', 'Betbunia', 'Ghagra', 'Kalampati', 'Sajek', 'Marisha'],
                    'Baghaichhari': ['Baghaichhari Pouroshova', 'Marisha', 'Sajek', 'Rupkari', 'Amtali', 'Barkal'],
                    'Langadu': ['Langadu Pouroshova', 'Aronghata', 'Mayani', 'Langadu', 'Bogachhari', 'Gulshakhali'],
                    'Naniarchar': ['Naniarchar Pouroshova', 'Burighat', 'Ghilachhari', 'Sabekhyong', 'Tintahari'],
                    'Rajasthali': ['Rajasthali Pouroshova', 'Bangalhalia', 'Gainda', 'Ghilachhari', 'Rajasthali'],
                    'Juraichhari': ['Juraichhari Pouroshova', 'Barkal', 'Dumdumya', 'Juraichhari', 'Sapchari']
                }
            },
            'Bandarban': {
                upazilas: {
                    'Bandarban Sadar': ['Bandarban Pouroshova', 'Rajbila', 'Kuhalong', 'Balaghata', 'Sualok', 'Tankabati'],
                    'Lama': ['Lama Pouroshova', 'Gozalia', 'Ruposhi', 'Aziznagar', 'Fasyakhali', 'Sarai'],
                    'Alikadam': ['Alikadam Pouroshova', 'Choykhyong', 'Naikhyongchari', 'Dokhin', 'Alikadam', 'Baishari'],
                    'Naikhongchhari': ['Naikhongchhari Pouroshova', 'Baishari', 'Dochhari', 'Ghandung', 'Sonaichhari'],
                    'Rowangchhari': ['Rowangchhari Pouroshova', 'Alikhong', 'Noapatang', 'Rowangchhari', 'Taracha'],
                    'Ruma': ['Ruma Pouroshova', 'Galengya', 'Paindu', 'Remakri', 'Ruma', 'Tongkaboti'],
                    'Thanchi': ['Thanchi Pouroshova', 'Balipara', 'Remakri', 'Thanchi', 'Tindu']
                }
            },
            'Noakhali': {
                upazilas: {
                    'Noakhali Sadar': ['Noakhali Pouroshova', 'Char Matua', 'Char Hazari', 'Noannoi', 'Binodpur', 'Dadpur'],
                    'Begumganj': ['Begumganj Pouroshova', 'Alaiarpur', 'Gopalpur', 'Mirwarishpur', 'Rajganj', 'Durgapur'],
                    'Companiganj': ['Companiganj Pouroshova', 'Char Elahi', 'Char Fakira', 'Musapur', 'Char Amanullah'],
                    'Chatkhil': ['Chatkhil Pouroshova', 'Khilpara', 'Panchgaon', 'Ramnarayanpur', 'Sahapur'],
                    'Senbagh': ['Senbagh Pouroshova', 'Bejbagh', 'Kadirpur', 'Mohammadpur', 'Narottampur'],
                    'Sonaimuri': ['Sonaimuri Pouroshova', 'Amishapara', 'Deoti', 'Jayag', 'Nadona', 'Sonapur'],
                    'Subarnachar': ['Subarnachar Pouroshova', 'Char Bata', 'Char Clerk', 'Char Jubilee', 'Char Wapda'],
                    'Kabirhat': ['Kabirhat Pouroshova', 'Chaprashirhat', 'Dhan Siri', 'Ghoshbag', 'Wahedpur'],
                    'Hatiya': ['Hatiya Pouroshova', 'Char Ishwar', 'Char King', 'Harni', 'Jahajmara', 'Tamaruddin']
                }
            },
            'Comilla': {
                upazilas: {
                    'Comilla Sadar': ['Comilla City Corporation', 'Kandirpar', 'Kotbari', 'Tomsom Bridge', 'Bagichagaon', 'Jhakuni'],
                    'Chandina': ['Chandina Pouroshova', 'Madhaiabazar', 'Mohichail', 'Shuagazi', 'Batakandi', 'Dollai Nawabpur'],
                    'Debidwar': ['Debidwar Pouroshova', 'Barura', 'Gouripur', 'Eliotganj', 'Mohanpur', 'Rasulpur'],
                    'Barura': ['Barura Pouroshova', 'Galimpur', 'Murdafarganj', 'Poyalgachha', 'Shahebabad'],
                    'Brahmanpara': ['Brahmanpara Pouroshova', 'Maijkhar', 'Shashidal', 'Sidlai', 'Sultanpur'],
                    'Burichang': ['Burichang Pouroshova', 'Bakshimul', 'Mainamati', 'Rajapur', 'Sholanal'],
                    'Chauddagram': ['Chauddagram Pouroshova', 'Alkara', 'Chiora', 'Gunabati', 'Sreepur'],
                    'Daudkandi': ['Daudkandi Pouroshova', 'Biteshwar', 'Gouripur', 'Mohammadpur', 'Sundalpur'],
                    'Homna': ['Homna Pouroshova', 'Banchharampur', 'Dulalpur', 'Mathabhanga', 'Nilakhi'],
                    'Laksam': ['Laksam Pouroshova', 'Ajgara', 'Laksam', 'Mudafarganj', 'Uttardah'],
                    'Muradnagar': ['Muradnagar Pouroshova', 'Andikot', 'Bangadda', 'Darora', 'Jahapur'],
                    'Nangalkot': ['Nangalkot Pouroshova', 'Adra', 'Nangalkot', 'Peria', 'Roykot'],
                    'Meghna': ['Meghna Pouroshova', 'Barakanda', 'Chandanpur', 'Kalir Bazar', 'Sarifpur'],
                    'Titas': ['Titas Pouroshova', 'Bitara', 'Jagatpur', 'Kadda', 'Podua']
                }
            },
            'Chandpur': {
                upazilas: {
                    'Chandpur Sadar': ['Chandpur Pouroshova', 'Bagadi', 'Baghadi', 'Rajrajeshwar', 'Shahmahmudpur'],
                    'Faridganj': ['Faridganj Pouroshova', 'Rupsha', 'Gridkaliandia', 'Islampur', 'Rampurbazar'],
                    'Haimchar': ['Haimchar Pouroshova', 'Charbhairabi', 'Gandharbapur', 'Nilkamal', 'Pashchim Barkul'],
                    'Haziganj': ['Haziganj Pouroshova', 'Bolakhal', 'Hatila', 'Kalyanpur', 'Mohanpur'],
                    'Kachua': ['Kachua Pouroshova', 'Ashrafpur', 'Gohat', 'Kadla', 'Sachar'],
                    'Matlab Dakshin': ['Matlab Dakshin Pouroshova', 'Narayanpur', 'Nawabad', 'Sultanabad', 'Uttar Nayergaon'],
                    'Matlab Uttar': ['Matlab Uttar Pouroshova', 'Chandpur', 'Durgapur', 'Fatehpur', 'Narayanpur'],
                    'Shahrasti': ['Shahrasti Pouroshova', 'Chitoshi', 'Islamabad', 'Khajuria', 'Uttar Shahrasti']
                }
            },
            'Brahmanbaria': {
                upazilas: {
                    'Brahmanbaria Sadar': ['Brahmanbaria Pouroshova', 'Basudeb', 'Machihata', 'Majlishpur', 'Sultanpur'],
                    'Akhaura': ['Akhaura Pouroshova', 'Dakshin Akhaura', 'Moniand', 'Mogra', 'Uttar Akhaura'],
                    'Bancharampur': ['Bancharampur Pouroshova', 'Dariadaulat', 'Pahariakandi', 'Salimabad', 'Sonarampur'],
                    'Kasba': ['Kasba Pouroshova', 'Bayek', 'Kuti', 'Mehari', 'Salimganj'],
                    'Nabinagar': ['Nabinagar Pouroshova', 'Barail', 'Laurfatehpur', 'Natghar', 'Shamgram'],
                    'Nasirnagar': ['Nasirnagar Pouroshova', 'Burishwar', 'Chapartala', 'Fandauk', 'Goalnagar'],
                    'Sarail': ['Sarail Pouroshova', 'Aruail', 'Kalikaccha', 'Noagaon', 'Shahbazpur'],
                    'Ashuganj': ['Ashuganj Pouroshova', 'Araishidha', 'Durgapur', 'Lalpur', 'Tarua'],
                    'Bijoynagar': ['Bijoynagar Pouroshova', 'Chandura', 'Harashpur', 'Pattan', 'Singerbil']
                }
            },
            'Feni': {
                upazilas: {
                    'Feni Sadar': ['Feni Pouroshova', 'Baligaon', 'Dhormapur', 'Kazirbag', 'Lemua'],
                    'Chhagalnaiya': ['Chhagalnaiya Pouroshova', 'Gopal', 'Mohammadpur', 'Pathannagar', 'Radhanagar'],
                    'Daganbhuiyan': ['Daganbhuiyan Pouroshova', 'Matubhuiyan', 'Purba Chandrapur', 'Rajapur', 'Sindurpur'],
                    'Parshuram': ['Parshuram Pouroshova', 'Anandapur', 'Baligaon', 'Mirzanagar', 'Ramnagar'],
                    'Sonagazi': ['Sonagazi Pouroshova', 'Amirabad', 'Char Chandia', 'Mangalkandi', 'Musapur'],
                    'Fulgazi': ['Fulgazi Pouroshova', 'Anandapur', 'Dharmapur', 'Munshirhat', 'Pachgachia']
                }
            },
            'Lakshmipur': {
                upazilas: {
                    'Lakshmipur Sadar': ['Lakshmipur Pouroshova', 'Bhabaniganj', 'Dattapara', 'Laharkandi', 'Shakchar'],
                    'Raipur': ['Raipur Pouroshova', 'Bhadur', 'Haydarganj', 'Nagerdighirpar', 'Rakhallia'],
                    'Ramganj': ['Ramganj Pouroshova', 'Darbeshpur', 'Kanchanpur', 'Noagaon', 'Sabujganj'],
                    'Ramgati': ['Ramgati Pouroshova', 'Char Abdullah', 'Char Algi', 'Char Ramiz', 'Sonapur'],
                    'Kamalnagar': ['Kamalnagar Pouroshova', 'Char Falcon', 'Char Kadira', 'Char Martin', 'Torabganj']
                }
            },
            'Khagrachhari': {
                upazilas: {
                    'Khagrachhari Sadar': ['Khagrachhari Pouroshova', 'Bhaibonchhara', 'Golabari', 'Kamalchari', 'Perachhara'],
                    'Dighinala': ['Dighinala Pouroshova', 'Babuchhara', 'Boalkhali', 'Merung', 'Taindong'],
                    'Lakshmichhari': ['Lakshmichhari Pouroshova', 'Barmachari', 'Dulyatali', 'Lakshmichhari', 'Ramgarh'],
                    'Mahalchhari': ['Mahalchhari Pouroshova', 'Kayangghat', 'Mahalchhari', 'Manikchari', 'Sindukchari'],
                    'Manikchhari': ['Manikchhari Pouroshova', 'Batnatali', 'Jogyachola', 'Tintahari', 'Vastali'],
                    'Matiranga': ['Matiranga Pouroshova', 'Amtali', 'Guimara', 'Matiranga', 'Tabalchhari'],
                    'Panchhari': ['Panchhari Pouroshova', 'Chengi', 'Karalchhari', 'Latiban', 'Panchhari'],
                    'Ramgarh': ['Ramgarh Pouroshova', 'Hafchari', 'Pathachhari', 'Ramgarh', 'Sindukchari']
                }
            }
        }
    },
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
    },
    'Rajshahi': {
        districts: {
            'Rajshahi': {
                upazilas: {
                    'Rajshahi City': ['Rajshahi City Corporation', 'Shaheb Bazar', 'Rampur', 'Kazla', 'Binodpur', 'Belpukur'],
                    'Paba': ['Paba Pouroshova', 'Katakhali', 'Harian', 'Nowhata', 'Parila', 'Damkura'],
                    'Godagari': ['Godagari Pouroshova', 'Premtoli', 'Mohonpur', 'Pakri', 'Rishikul', 'Deopara'],
                    'Tanore': ['Tanore Pouroshova', 'Chanduria', 'Talanda', 'Mundumala', 'Kalma', 'Badhair'],
                    'Bagmara': ['Bagmara Pouroshova', 'Hamirkutsa', 'Ghasigram', 'Jogipara', 'Sreepur', 'Gobindapara'],
                    'Durgapur': ['Durgapur Pouroshova', 'Deluabari', 'Joynagar', 'Maria', 'Sultanpur', 'Paulpara'],
                    'Mohanpur': ['Mohanpur Pouroshova', 'Mougachhi', 'Bakshimail', 'Jahanabad', 'Holdigachi', 'Kesharhat'],
                    'Puthia': ['Puthia Pouroshova', 'Baragachhi', 'Baneshwar', 'Salua', 'Belpukur', 'Nimpara'],
                    'Bagha': ['Bagha Pouroshova', 'Arani', 'Pakuria', 'Dhuroil', 'Gorgori', 'Bhawaniganj'],
                    'Charghat': ['Charghat Pouroshova', 'Yousufpur', 'Salua', 'Sardah', 'Nimpara', 'Vialuxmipur']
                }
            },
            'Chapainawabganj': {
                upazilas: {
                    'Chapainawabganj Sadar': ['Chapainawabganj Pouroshova', 'Ranihati', 'Gobratala', 'Sundarpur', 'Mobarakpur', 'Jhilim'],
                    'Shibganj': ['Shibganj Pouroshova', 'Kansat', 'Manaksha', 'Shahbazpur', 'Mobarakpur', 'Chakkirti'],
                    'Nachole': ['Nachole Pouroshova', 'Fatehpur', 'Nezampur', 'Rohanpur', 'Kasba', 'Nachol'],
                    'Gomastapur': ['Gomastapur Pouroshova', 'Radhanagar', 'Alinagar', 'Boalia', 'Parbatipur', 'Rahanpur'],
                    'Bholahat': ['Bholahat Pouroshova', 'Jambaria', 'Daldali', 'Gohalbari', 'Nijampur', 'Maharajpur']
                }
            },
            'Naogaon': {
                upazilas: {
                    'Naogaon Sadar': ['Naogaon Pouroshova', 'Hapania', 'Dubalhati', 'Tilakpur', 'Barshail', 'Balihar'],
                    'Mohadevpur': ['Mohadevpur Pouroshova', 'Hatur', 'Chandas', 'Cheragpur', 'Safapur', 'Roygaon'],
                    'Manda': ['Manda Pouroshova', 'Kusumba', 'Paranpur', 'Bhalain', 'Nurullabad', 'Kalikapur'],
                    'Niamatpur': ['Niamatpur Pouroshova', 'Rasulpur', 'Parail', 'Sreemantapur', 'Tilna', 'Hajinagar'],
                    'Atrai': ['Atrai Pouroshova', 'Panchupur', 'Sahagola', 'Maniari', 'Dhalahar', 'Brahmapur'],
                    'Raninagar': ['Raninagar Pouroshova', 'Kashimpur', 'Gona', 'Parail', 'Ekdala', 'Baktarpur'],
                    'Patnitala': ['Patnitala Pouroshova', 'Nirmail', 'Dibar', 'Akbarpur', 'Matindar', 'Krishnapur'],
                    'Dhamoirhat': ['Dhamoirhat Pouroshova', 'Alampur', 'Umar', 'Jahanpur', 'Isabpur', 'Agra'],
                    'Sapahar': ['Sapahar Pouroshova', 'Tilna', 'Aihai', 'Goala', 'Shiranti', 'Patari'],
                    'Porsha': ['Porsha Pouroshova', 'Nitpur', 'Tentulia', 'Ganguria', 'Chhaor', 'Bisha'],
                    'Badalgachhi': ['Badalgachhi Pouroshova', 'Mithapur', 'Paharpur', 'Balubhara', 'Adhaipur', 'Bilasbari']
                }
            },
            'Natore': {
                upazilas: {
                    'Natore Sadar': ['Natore Pouroshova', 'Dighapatia', 'Baraigram', 'Kafuria', 'Halsa', 'Madhnagar'],
                    'Singra': ['Singra Pouroshova', 'Dahia', 'Sukash', 'Chamari', 'Chaugram', 'Hatiandaha'],
                    'Baraigram': ['Baraigram Pouroshova', 'Joari', 'Zonail', 'Majgaon', 'Bonpara', 'Gopalpur'],
                    'Bagatipara': ['Bagatipara Pouroshova', 'Dayarampur', 'Panka', 'Jamnagar', 'Faguardiar', 'Lalpur'],
                    'Lalpur': ['Lalpur Pouroshova', 'Arbab', 'Duaria', 'Gopalpur', 'Iswardi', 'Kadimchilan'],
                    'Gurudaspur': ['Gurudaspur Pouroshova', 'Dayarampur', 'Laxmipur', 'Nazirpur', 'Biaghat', 'Chapila']
                }
            },
            'Pabna': {
                upazilas: {
                    'Pabna Sadar': ['Pabna Pouroshova', 'Ataikula', 'Maligachha', 'Sadullahpur', 'Dapunia', 'Hemayetpur'],
                    'Ishwardi': ['Ishwardi Pouroshova', 'Paksey', 'Dashuria', 'Muladuli', 'Lakshmipur', 'Sara'],
                    'Santhia': ['Santhia Pouroshova', 'Nagdemra', 'Bhulbaria', 'Manikhat', 'Dulai', 'Ataikula'],
                    'Bera': ['Bera Pouroshova', 'Chakla', 'Haturia Nakalia', 'Puran Bharenga', 'Masumdia', 'Kytola'],
                    'Sujanagar': ['Sujanagar Pouroshova', 'Sagarkandi', 'Manikhat', 'Dulai', 'Nazirganj', 'Satbaria'],
                    'Chatmohar': ['Chatmohar Pouroshova', 'Bilchalan', 'Danthia', 'Faridpur', 'Handial', 'Mulgram'],
                    'Faridpur': ['Faridpur Pouroshova', 'Brilahiribari', 'Hadal', 'Demra', 'Ataikula', 'Bhangura'],
                    'Atgharia': ['Atgharia Pouroshova', 'Chandba', 'Debottar', 'Ekdanta', 'Lakshmipur', 'Majhpara'],
                    'Bhangura': ['Bhangura Pouroshova', 'Dilpasar', 'Khanmarich', 'Parbhangura', 'Chakla', 'Bhangura']
                }
            },
            'Sirajganj': {
                upazilas: {
                    'Sirajganj Sadar': ['Sirajganj Pouroshova', 'Bagbati', 'Bahuli', 'Mechhra', 'Ratankandi', 'Saidabad'],
                    'Shahjadpur': ['Shahjadpur Pouroshova', 'Gala', 'Kayempur', 'Potajia', 'Rupabati', 'Sonamukhi'],
                    'Ullapara': ['Ullapara Pouroshova', 'Bangala', 'Udhunia', 'Ramkrishnapur', 'Salanga', 'Hatikumrul'],
                    'Kamarkhanda': ['Kamarkhanda Pouroshova', 'Bhadraghat', 'Khasrajbari', 'Nalka', 'Soydabad', 'Jamtail'],
                    'Kazipur': ['Kazipur Pouroshova', 'Chalitadanga', 'Khasrajbari', 'Maijbari', 'Monsur Nagar', 'Subhagachha'],
                    'Raiganj': ['Raiganj Pouroshova', 'Baradhul', 'Dhamainagar', 'Pangashi', 'Sonakhara', 'Umarpur'],
                    'Belkuchi': ['Belkuchi Pouroshova', 'Baradhul', 'Dhukuriabera', 'Doulatpur', 'Rajapur', 'Sthal'],
                    'Chauhali': ['Chauhali Pouroshova', 'Bagutia', 'Khaskaulia', 'Omarpur', 'Saydabad', 'Sthal'],
                    'Tarash': ['Tarash Pouroshova', 'Baradhul', 'Madhainagar', 'Magura Binod', 'Naogaon', 'Talam']
                }
            },
            'Joypurhat': {
                upazilas: {
                    'Joypurhat Sadar': ['Joypurhat Pouroshova', 'Bambu', 'Dhalahar', 'Panchbibi', 'Punat', 'Zindarpur'],
                    'Akkelpur': ['Akkelpur Pouroshova', 'Gopinathpur', 'Matrai', 'Paikartoli', 'Tilakpur', 'Uttargram'],
                    'Kalai': ['Kalai Pouroshova', 'Alampur', 'Atapur', 'Barail', 'Dhalahar', 'Punot'],
                    'Khetlal': ['Khetlal Pouroshova', 'Alampur', 'Atapur', 'Bhadsha', 'Mamudpur', 'Punot'],
                    'Panchbibi': ['Panchbibi Pouroshova', 'Amdai', 'Balighata', 'Dharanji', 'Mohammadpur', 'Kusumba']
                }
            },
            'Bogra': {
                upazilas: {
                    'Bogra Sadar': ['Bogra Pouroshova', 'Mathura', 'Sekherkola', 'Erulia', 'Nungola', 'Lahiripara'],
                    'Gabtali': ['Gabtali Pouroshova', 'Sonatala', 'Dhunat', 'Sukhanpukur', 'Naruamala', 'Mahasthan'],
                    'Shibganj': ['Shibganj Pouroshova', 'Mokamtala', 'Sariakandi', 'Bohail', 'Deuli', 'Majhira'],
                    'Shajahanpur': ['Shajahanpur Pouroshova', 'Erulia', 'Amrool', 'Shabgram', 'Nungola', 'Lahiripara'],
                    'Kahaloo': ['Kahaloo Pouroshova', 'Durgahata', 'Jamgaon', 'Malancha', 'Kalerpara', 'Chupinagar'],
                    'Nandigram': ['Nandigram Pouroshova', 'Bhatra', 'Burail', 'Thalta', 'Bhatgram', 'Lahiripara'],
                    'Adamdighi': ['Adamdighi Pouroshova', 'Chapapur', 'Kundagram', 'Nasratpur', 'Santahar', 'Shantahar'],
                    'Dhunat': ['Dhunat Pouroshova', 'Bhandarbari', 'Gosainbari', 'Mathurapur', 'Nimgachi', 'Talora'],
                    'Sherpur': ['Sherpur Pouroshova', 'Bishalpur', 'Garidaha', 'Khamarkandi', 'Kusumbi', 'Mirzapur'],
                    'Sonatala': ['Sonatala Pouroshova', 'Balua', 'Digdair', 'Pakulla', 'Tekani Chukinagar', 'Zianagar'],
                    'Sariakandi': ['Sariakandi Pouroshova', 'Bohail', 'Chaluabari', 'Fulbari', 'Kamalpur', 'Narchi'],
                    'Dhunot': ['Dhunot Pouroshova', 'Bhandarbari', 'Gosainbari', 'Mathurapur', 'Nimgachi', 'Talora']
                }
            }
        }
    },
    'Sylhet': {
        districts: {
            'Sylhet': {
                upazilas: {
                    'Sylhet Sadar': ['Sylhet City Corporation', 'Ambarkhana', 'Zindabazar', 'Shibganj', 'Tukerbazar', 'Kanishail', 'Khadimnagar'],
                    'South Surma': ['South Surma Pouroshova', 'Mogla', 'Tetli', 'Kuchai', 'Daudpur', 'Jalalpur', 'Zakiganj'],
                    'Balaganj': ['Balaganj Pouroshova', 'Omarpur', 'Golapganj', 'Dayamir', 'Paschim Gouripur', 'Purba Gouripur', 'Sadipur'],
                    'Bishwanath': ['Bishwanath Pouroshova', 'Dashghar', 'Rampasha', 'Deokalas', 'Daulatpur', 'Khazanchi', 'Alankari'],
                    'Companiganj': ['Companiganj Pouroshova', 'Islampur', 'Telikhal', 'Ranikhai', 'Ichhamati', 'Dubag', 'East Islampur'],
                    'Fenchuganj': ['Fenchuganj Pouroshova', 'Maijgaon', 'Gilachhara', 'Fenchuganj', 'Uttar Kushiara', 'Uttar Fenchuganj'],
                    'Golapganj': ['Golapganj Pouroshova', 'Amjad', 'Bagha', 'Bhadeshwar', 'Dhakadakshin', 'Fulbari', 'Lakshanaband'],
                    'Gowainghat': ['Gowainghat Pouroshova', 'Alirgaon', 'Fatehpur', 'Lengura', 'Nandirgaon', 'Paschim Jaflong', 'Purba Jaflong'],
                    'Jaintiapur': ['Jaintiapur Pouroshova', 'Charikatha', 'Darbast', 'Fatehpur', 'Chiknagul', 'Jaintiapur', 'Nijpat'],
                    'Kanaighat': ['Kanaighat Pouroshova', 'Bara Thakuri', 'Dakshin Banigram', 'Jhingabari', 'Kanaighat', 'Paschim Laxmiprashad'],
                    'Zakiganj': ['Zakiganj Pouroshova', 'Barahal', 'Bara Thakuri', 'Kajalshah', 'Kaskanakpur', 'Manikpur', 'Sultanpur']
                }
            },
            'Moulvibazar': {
                upazilas: {
                    'Moulvibazar Sadar': ['Moulvibazar Pouroshova', 'Khalilpur', 'Manumukh', 'Kamalpur', 'Akhailkura', 'Amtail', 'Chandnighat'],
                    'Sreemangal': ['Sreemangal Pouroshova', 'Satgaon', 'Kalighat', 'Ashidron', 'Bhunabir', 'Kashipur', 'Rajghat'],
                    'Kulaura': ['Kulaura Pouroshova', 'Prithimpasha', 'Ramnagar', 'Hajipur', 'Bhukshimail', 'Kadirpur', 'Tilagaon'],
                    'Kamalganj': ['Kamalganj Pouroshova', 'Shamshernagar', 'Madhabpur', 'Islampur', 'Alinagar', 'Patanushar', 'Rahimpur'],
                    'Rajnagar': ['Rajnagar Pouroshova', 'Tengra', 'Munshi Bazar', 'Fatehpur', 'Kamarchak', 'Mansurnagar', 'Uttarbhag'],
                    'Barlekha': ['Barlekha Pouroshova', 'Barni', 'Dakshinbhag', 'Dakshinbhag Uttar', 'Nij Bahadurpur', 'Sujanagar'],
                    'Juri': ['Juri Pouroshova', 'Paschim Juri', 'Purba Juri', 'Fultala', 'Goalbari', 'Sagarnal']
                }
            },
            'Sunamganj': {
                upazilas: {
                    'Sunamganj Sadar': ['Sunamganj Pouroshova', 'Patharia', 'Jahangirnagar', 'Shimulbak', 'Mohonpur', 'Rangarchar', 'Surma'],
                    'Chhatak': ['Chhatak Pouroshova', 'Gabindaganj', 'Islamabad', 'Noarai', 'Charmohalla', 'Dularbazar', 'Kalaruka'],
                    'Derai': ['Derai Pouroshova', 'Karimpur', 'Jagannathpur', 'Rajanagar', 'Bhati Para', 'Charnarchar', 'Taral'],
                    'Dharampasha': ['Dharampasha Pouroshova', 'Joysree', 'Sukhair Rajapur', 'Uttar Badaghat', 'Dakshin Badaghat', 'Selborosh'],
                    'Dowarabazar': ['Dowarabazar Pouroshova', 'Bangla Bazar', 'Dohalia', 'Lakshmipur', 'Mannargaon', 'Pandargaon'],
                    'Jagannathpur': ['Jagannathpur Pouroshova', 'Asharkandi', 'Haldipur', 'Pailgaon', 'Raniganj', 'Syedpur'],
                    'Jamalganj': ['Jamalganj Pouroshova', 'Beheli', 'Fenarbak', 'Jamalganj', 'Sachna Bazar', 'Vimkhali'],
                    'Sulla': ['Sulla Pouroshova', 'Atgaon', 'Habibpur', 'Protappur', 'Sulla', 'Uttar Sulla'],
                    'Tahirpur': ['Tahirpur Pouroshova', 'Balijuri', 'Dakshin Sreepur', 'Tahirpur', 'Uttar Sreepur', 'Badaghat'],
                    'Bishwambarpur': ['Bishwambarpur Pouroshova', 'Dakshin Badaghat', 'Dhonpur', 'Fatehpur', 'Palash', 'Shimulbak']
                }
            },
            'Habiganj': {
                upazilas: {
                    'Habiganj Sadar': ['Habiganj Pouroshova', 'Gopaya', 'Richi', 'Laskarpur', 'Nurpur', 'Poil', 'Shahjibazar'],
                    'Nabiganj': ['Nabiganj Pouroshova', 'Bausha', 'Inathganj', 'Digalbak', 'Gaznaipur', 'Kaliarbhanga', 'Paniumda'],
                    'Madhabpur': ['Madhabpur Pouroshova', 'Shahjahanpur', 'Andiura', 'Noapara', 'Bulla', 'Chowmuhani', 'Jagadishpur'],
                    'Chunarughat': ['Chunarughat Pouroshova', 'Chunarughat', 'Deorgach', 'Mirpur', 'Paikpara', 'Ranigaon', 'Ubahata'],
                    'Lakhai': ['Lakhai Pouroshova', 'Badalpur', 'Brahmanpara', 'Karab', 'Lakhai', 'Murakari'],
                    'Ajmiriganj': ['Ajmiriganj Pouroshova', 'Ajmiriganj', 'Baniachong', 'Shibpasha', 'Kakailseo', 'Montala'],
                    'Baniachong': ['Baniachong Pouroshova', 'Baraiuri', 'Daulatpur', 'Kagapasha', 'Khagaura', 'Pailarkandi'],
                    'Bahubal': ['Bahubal Pouroshova', 'Bahubal', 'Mirpur', 'Putijuri', 'Snanghat', 'Sujatpur']
                }
            }
        }
    },
    'Rangpur': {
        districts: {
            'Rangpur': {
                upazilas: {
                    'Rangpur Sadar': ['Rangpur City Corporation', 'Dhap', 'Alamnagar', 'Shapara', 'Mominpur', 'Tampat', 'Chandanpat'],
                    'Badarganj': ['Badarganj Pouroshova', 'Kishorganj', 'Radhanagar', 'Gopinathpur', 'Ramnathpur', 'Madhupur', 'Kutubpur'],
                    'Mithapukur': ['Mithapukur Pouroshova', 'Baldipukur', 'Kafrikhal', 'Jaigir', 'Baluya', 'Chenga', 'Mirjapur'],
                    'Pirgachha': ['Pirgachha Pouroshova', 'Tambulpur', 'Chandanpat', 'Kabilpur', 'Raypur', 'Shanerhat', 'Annodanagar'],
                    'Taraganj': ['Taraganj Pouroshova', 'Ekarchali', 'Kursha', 'Alampur', 'Sayar', 'Hariarkuthi', 'Udoypur'],
                    'Gangachara': ['Gangachara Pouroshova', 'Alambiditor', 'Gajaghanta', 'Kolkond', 'Lakkhitari', 'Marania', 'Nohali'],
                    'Kaunia': ['Kaunia Pouroshova', 'Anjuman', 'Balarhat', 'Kaunia', 'Kursha', 'Shahidbag', 'Tepamodhupur'],
                    'Pirganj': ['Pirganj Pouroshova', 'Chaitrakol', 'Kabilpur', 'Mithipur', 'Parul', 'Ramnathpur', 'Tukuria']
                }
            },
            'Dinajpur': {
                upazilas: {
                    'Dinajpur Sadar': ['Dinajpur Pouroshova', 'Pulhat', 'Chehelgazi', 'Basherhat', 'Auliapur', 'Kamalpur', 'Sundarpur'],
                    'Birampur': ['Birampur Pouroshova', 'Katla', 'Mohammadpur', 'Dior', 'Bhandardaha', 'Katla', 'Shimulbari'],
                    'Bochaganj': ['Bochaganj Pouroshova', 'Nashratpur', 'Mohonpur', 'Atgaon', 'Gosaipur', 'Mohonpur', 'Ranipur'],
                    'Kaharole': ['Kaharole Pouroshova', 'Sundarban', 'Rasulpur', 'Targaon', 'Mukundapur', 'Rampur', 'Shonahar'],
                    'Parbatipur': ['Parbatipur Pouroshova', 'Chandipur', 'Habra', 'Manmathpur', 'Mustafapur', 'Ramchandrapur', 'Shibrampur'],
                    'Birganj': ['Birganj Pouroshova', 'Bhognagar', 'Dharmapur', 'Faridpur', 'Khattamadhobpur', 'Mohammadpur', 'Raniganj'],
                    'Chirirbandar': ['Chirirbandar Pouroshova', 'Abdulpur', 'Alihat', 'Auliapukur', 'Bhitarband', 'Isabpur', 'Nashratpur'],
                    'Ghoraghat': ['Ghoraghat Pouroshova', 'Bulakipur', 'Palsha', 'Paltapur', 'Singra', 'Shibrampur', 'Taluk Kanupur'],
                    'Hakimpur': ['Hakimpur Pouroshova', 'Alihat', 'Boalder', 'Daulatpur', 'Khatta Madhobpur', 'Khayerbari', 'Shibrampur'],
                    'Khansama': ['Khansama Pouroshova', 'Angarpara', 'Goaldihi', 'Khanpur', 'Rampur', 'Shankarpur', 'Uthrail'],
                    'Nawabganj': ['Nawabganj Pouroshova', 'Binodnagar', 'Gopalpur', 'Joypur', 'Kushdaha', 'Satnala', 'Sundorbon'],
                    'Phulbari': ['Phulbari Pouroshova', 'Aminpur', 'Belaichandi', 'Champapur', 'Dhontola', 'Shibnagor', 'Sujalpur'],
                    'Setabganj': ['Setabganj Pouroshova', 'Amirpur', 'Bochaganj', 'Farakkabad', 'Mahmudpur', 'Sator', 'Setabganj']
                }
            },
            'Kurigram': {
                upazilas: {
                    'Kurigram Sadar': ['Kurigram Pouroshova', 'Belgachha', 'Panchgachhi', 'Holokhana', 'Ghogadaha', 'Kanthalbari', 'Mogalbasa'],
                    'Bhurungamari': ['Bhurungamari Pouroshova', 'Andharirjhar', 'Char Bhurungamari', 'Shilkhuri', 'Bangasonahat', 'Paikerchhara', 'Tilai'],
                    'Ulipur': ['Ulipur Pouroshova', 'Bazra', 'Tabakpur', 'Daldalia', 'Dhamsreni', 'Gunaigachh', 'Tetrai'],
                    'Chilmari': ['Chilmari Pouroshova', 'Ashtamir Char', 'Nayerhat', 'Ramna', 'Raniganj', 'Thanahat'],
                    'Phulbari': ['Phulbari Pouroshova', 'Baravita', 'Kashipur', 'Naodanga', 'Phulbari', 'Shimulbari'],
                    'Nageshwari': ['Nageshwari Pouroshova', 'Balabari', 'Bamondanga', 'Berubari', 'Hasnabad', 'Kochakata', 'Newashi'],
                    'Rajarhat': ['Rajarhat Pouroshova', 'Bidyananda', 'Chakirpashar', 'Gharialdanga', 'Nazimkhan', 'Umarmajid'],
                    'Rajibpur': ['Rajibpur Pouroshova', 'Kodalkati', 'Mohanganj', 'Rajibpur', 'Kodalkati', 'Mohanganj'],
                    'Rowmari': ['Rowmari Pouroshova', 'Bandaber', 'Jadurchar', 'Rowmari', 'Shaptibari', 'Thetrai']
                }
            },
            'Gaibandha': {
                upazilas: {
                    'Gaibandha Sadar': ['Gaibandha Pouroshova', 'Lakshmipur', 'Malibari', 'Katabari', 'Badiakhali', 'Ghagoa', 'Kamarjani'],
                    'Sundarganj': ['Sundarganj Pouroshova', 'Belka', 'Dhaperhat', 'Ramjibon', 'Chandipur', 'Kapasia', 'Tarapur'],
                    'Palashbari': ['Palashbari Pouroshova', 'Mohimaganj', 'Bharatkhali', 'Haripur', 'Betkapa', 'Manoharpur', 'Kishoregari'],
                    'Gobindaganj': ['Gobindaganj Pouroshova', 'Fassipur', 'Kamardaha', 'Katabari', 'Mahimaganj', 'Rakhalburuj', 'Saghata'],
                    'Fulchhari': ['Fulchhari Pouroshova', 'Erendabari', 'Fazlupur', 'Fulchhari', 'Gazaria', 'Kanchipara'],
                    'Sadullapur': ['Sadullapur Pouroshova', 'Damodarpur', 'Faridpur', 'Idilpur', 'Jamalpur', 'Naldanga', 'Rasulpur'],
                    'Saghata': ['Saghata Pouroshova', 'Bonarpara', 'Ghuridaha', 'Holdia', 'Jumarbari', 'Kamalerpara', 'Muktinagar']
                }
            },
            'Thakurgaon': {
                upazilas: {
                    'Thakurgaon Sadar': ['Thakurgaon Pouroshova', 'Akhanagar', 'Begunbari', 'Chilarang', 'Debipur', 'Gareya', 'Jamalpur'],
                    'Baliadangi': ['Baliadangi Pouroshova', 'Baragaon', 'Dhantala', 'Duosuo', 'Paria', 'Rator', 'Lebugaon'],
                    'Haripur': ['Haripur Pouroshova', 'Amgaon', 'Bakua', 'Dangi', 'Gedura', 'Lehemba', 'Saidpur'],
                    'Pirganj': ['Pirganj Pouroshova', 'Bairchuna', 'Bhomradaha', 'Daulatpur', 'Jabarhat', 'Khangaon', 'Sengaon'],
                    'Ranisankail': ['Ranisankail Pouroshova', 'Bachor', 'Dharmagarh', 'Hossaingaon', 'Nekmarad', 'Nonduar', 'Vanor']
                }
            },
            'Panchagarh': {
                upazilas: {
                    'Panchagarh Sadar': ['Panchagarh Pouroshova', 'Amarkhana', 'Chaklahat', 'Garinabari', 'Hafizabad', 'Kamat Kajol', 'Magura'],
                    'Atwari': ['Atwari Pouroshova', 'Alowakhowa', 'Balarampur', 'Dhamor', 'Mirzapur', 'Radhanagar', 'Toria'],
                    'Boda': ['Boda Pouroshova', 'Benghari', 'Chandanbari', 'Jholaishal', 'Kajoldighi', 'Marea', 'Sakoa'],
                    'Debiganj': ['Debiganj Pouroshova', 'Chilahati', 'Dandopal', 'Debiduba', 'Pamuli', 'Sonahar', 'Tepriganj'],
                    'Tetulia': ['Tetulia Pouroshova', 'Bhojanpur', 'Buraburi', 'Debnagar', 'Salbahan', 'Tirnoihat', 'Vitorgarh']
                }
            },
            'Nilphamari': {
                upazilas: {
                    'Nilphamari Sadar': ['Nilphamari Pouroshova', 'Bangalipur', 'Chapra Sarnjami', 'Itakhola', 'Kunda Pukur', 'Palashbari', 'Sonaroy'],
                    'Domar': ['Domar Pouroshova', 'Bamunia', 'Boragari', 'Gomnati', 'Harinchara', 'Ketkibari', 'Sonaroy'],
                    'Dimla': ['Dimla Pouroshova', 'Gayabari', 'Jhunagach', 'Khalisha', 'Naotara', 'Paschim Chhatnai', 'Tepa Kharibari'],
                    'Jaldhaka': ['Jaldhaka Pouroshova', 'Balagram', 'Daoabari', 'Golna', 'Kaimari', 'Mirganj', 'Shimulbari'],
                    'Kishoreganj': ['Kishoreganj Pouroshova', 'Bahagili', 'Chandkhana', 'Garagram', 'Magura', 'Nitai', 'Ranachandi'],
                    'Saidpur': ['Saidpur Pouroshova', 'Bangalipur', 'Botlagari', 'Kamarpukur', 'Khata Madhupur', 'Paurasha', 'Tengonmari']
                }
            },
            'Lalmonirhat': {
                upazilas: {
                    'Lalmonirhat Sadar': ['Lalmonirhat Pouroshova', 'Gokunda', 'Harati', 'Kulaghat', 'Mogolhat', 'Panchagram', 'Rajpur'],
                    'Aditmari': ['Aditmari Pouroshova', 'Bhadai', 'Durgapur', 'Goddimari', 'Kamlabari', 'Mohishkhocha', 'Saptibari'],
                    'Kaliganj': ['Kaliganj Pouroshova', 'Bhotemari', 'Chalbala', 'Dalagram', 'Goral', 'Madati', 'Tushbhandar'],
                    'Hatibandha': ['Hatibandha Pouroshova', 'Barakhata', 'Dawabari', 'Goddimari', 'Patikapara', 'Sindurna', 'Tongvhanga'],
                    'Patgram': ['Patgram Pouroshova', 'Baura', 'Burimari', 'Dahagram', 'Jagatber', 'Jongra', 'Sreerampur']
                }
            }
        }
    },
    'Mymensingh': {
        districts: {
            'Mymensingh': {
                upazilas: {
                    'Mymensingh Sadar': ['Mymensingh City Corporation', 'Chorpara', 'Ganginar Par', 'Akua', 'Borar Char', 'Dapunia', 'Khagdahar'],
                    'Bhaluka': ['Bhaluka Pouroshova', 'Mallikbari', 'Habirbari', 'Birunia', 'Dakatia', 'Kachina', 'Uthura'],
                    'Trishal': ['Trishal Pouroshova', 'Kanthal', 'Dhanikhola', 'Bailar', 'Mathbari', 'Mokshapur', 'Rampur'],
                    'Muktagachha': ['Muktagachha Pouroshova', 'Kutubpur', 'Tarati', 'Bashati', 'Kashimpur', 'Mankon', 'Daogaon'],
                    'Fulbaria': ['Fulbaria Pouroshova', 'Bakta', 'Enayetpur', 'Kaladaha', 'Kushmail', 'Radhakanai', 'Rangamatia'],
                    'Gafargaon': ['Gafargaon Pouroshova', 'Dobadia', 'Jessora', 'Langair', 'Mashakhali', 'Panchbagh', 'Usthi'],
                    'Gauripur': ['Gauripur Pouroshova', 'Bhangnamari', 'Bokainagar', 'Douhakhola', 'Sidhla', 'Ramgopalpur'],
                    'Ishwarganj': ['Ishwarganj Pouroshova', 'Atharabari', 'Jatia', 'Maijbagh', 'Rajibpur', 'Sarisha', 'Tarundia'],
                    'Nandail': ['Nandail Pouroshova', 'Achargaon', 'Chandipasha', 'Gangail', 'Jahangirpur', 'Kharua', 'Sherpur'],
                    'Phulpur': ['Phulpur Pouroshova', 'Balia', 'Baola', 'Bhaitkandi', 'Kakni', 'Phulpur', 'Rupasi'],
                    'Haluaghat': ['Haluaghat Pouroshova', 'Amtail', 'Bhutia', 'Gazirbhita', 'Jugli', 'Narail', 'Swadeshi'],
                    'Dhobaura': ['Dhobaura Pouroshova', 'Dhobaura', 'Goatala', 'Ghoshgaon', 'Porakandulia', 'Sahanati']
                }
            },
            'Jamalpur': {
                upazilas: {
                    'Jamalpur Sadar': ['Jamalpur Pouroshova', 'Narundi', 'Kendua', 'Digpait', 'Ghoradhap', 'Itail', 'Meshta'],
                    'Dewanganj': ['Dewanganj Pouroshova', 'Chukaibari', 'Dangdhara', 'Bahadurabad', 'Char Amkhawa', 'Hatibanga', 'Parram'],
                    'Islampur': ['Islampur Pouroshova', 'Patharsi', 'Kulkandi', 'Chinaduli', 'Belgachha', 'Gaibandha', 'Noarpara'],
                    'Madarganj': ['Madarganj Pouroshova', 'Adarvita', 'Balijuri', 'Sidhuli', 'Danua', 'Gunaritala', 'Jorekhali'],
                    'Sarishabari': ['Sarishabari Pouroshova', 'Pogaldigha', 'Bhatara', 'Kamrabad', 'Aona', 'Doail', 'Satpoa'],
                    'Melandaha': ['Melandaha Pouroshova', 'Adra', 'Durmuth', 'Fulkocha', 'Jharuarbhanga', 'Kulia', 'Mahmudpur'],
                    'Bakshiganj': ['Bakshiganj Pouroshova', 'Battajore', 'Merurchar', 'Nilakshmia', 'Sadhurpara', 'Shuampur']
                }
            },
            'Netrokona': {
                upazilas: {
                    'Netrokona Sadar': ['Netrokona Pouroshova', 'Amtala', 'Challisha', 'Medni', 'Madan', 'Rauha', 'Singher Bangla'],
                    'Atpara': ['Atpara Pouroshova', 'Sukhari', 'Baniyajan', 'Teligati', 'Dalpa', 'Lunesshor', 'Suair'],
                    'Barhatta': ['Barhatta Pouroshova', 'Singdha', 'Sahata', 'Roailbari', 'Asma', 'Baushi', 'Hogla'],
                    'Durgapur': ['Durgapur Pouroshova', 'Birisiri', 'Chandigarh', 'Kakoirgora', 'Kullagora', 'Oshtogram'],
                    'Kendua': ['Kendua Pouroshova', 'Chirang', 'Gaglajur', 'Kandiura', 'Muzafarpur', 'Noapara', 'Rangchhati'],
                    'Kalmakanda': ['Kalmakanda Pouroshova', 'Barkhapon', 'Duoj', 'Lengura', 'Nazirpur', 'Pogla', 'Rangchati'],
                    'Khaliajuri': ['Khaliajuri Pouroshova', 'Chakua', 'Gazipur', 'Khaliajuri', 'Krishnapur', 'Mendipur'],
                    'Madan': ['Madan Pouroshova', 'Chandgao', 'Fatehpur', 'Madan', 'Nayekpur', 'Singdha'],
                    'Mohanganj': ['Mohanganj Pouroshova', 'Baratali', 'Maghan', 'Rouha', 'Suair', 'Teligati'],
                    'Purbadhala': ['Purbadhala Pouroshova', 'Amtala', 'Jaria', 'Purbadhala', 'Raypur', 'Tethulia']
                }
            },
            'Sherpur': {
                upazilas: {
                    'Sherpur Sadar': ['Sherpur Pouroshova', 'Bajitkhila', 'Kamarer Char', 'Gazir Khamar', 'Pakuria', 'Rauha', 'Sreebardi'],
                    'Nakla': ['Nakla Pouroshova', 'Gourdwar', 'Pathakata', 'Baneshwardi', 'Chandrakona', 'Pathakata', 'Talki'],
                    'Nalitabari': ['Nalitabari Pouroshova', 'Noyabil', 'Poragaon', 'Ramchandrakura', 'Bagber', 'Juganiya', 'Rupnarayankura'],
                    'Sreebardi': ['Sreebardi Pouroshova', 'Bhelua', 'Garjaripa', 'Jhenaigati', 'Kalaspur', 'Ranishimul', 'Tantihati'],
                    'Jhenaigati': ['Jhenaigati Pouroshova', 'Dhanshail', 'Dupuria', 'Hatibanda', 'Malijhikanda', 'Nalkura']
                }
            }
        }
    },
    'Barisal': {
        districts: {
            'Barisal': {
                upazilas: {
                    'Barisal Sadar': ['Barisal City Corporation', 'Rahamtpur', 'Kashipur', 'Charbaria', 'Chandpura', 'Tungibaria'],
                    'Agailjhara': ['Agailjhara Pouroshova', 'Bagdha', 'Gaila', 'Goila', 'Rajihar', 'Ratnapur'],
                    'Babuganj': ['Babuganj Pouroshova', 'Chandpasha', 'Dehergati', 'Madhabpasha', 'Rahmatpur', 'Thakurhat'],
                    'Bakerganj': ['Bakerganj Pouroshova', 'Charadi', 'Darial', 'Faridpur', 'Garuria', 'Kabai'],
                    'Banaripara': ['Banaripara Pouroshova', 'Banaripara', 'Chakhar', 'Iluhar', 'Saliabakpur', 'Udykhati'],
                    'Gaurnadi': ['Gaurnadi Pouroshova', 'Batajor', 'Chandshi', 'Khanjapur', 'Mahilara', 'Nalchira'],
                    'Hizla': ['Hizla Pouroshova', 'Dhulkhola', 'Guabaria', 'Harinathpur', 'Memania', 'Shankar'],
                    'Mehendiganj': ['Mehendiganj Pouroshova', 'Alimabad', 'Chandpur', 'Jangalia', 'Lata', 'Ulania'],
                    'Muladi': ['Muladi Pouroshova', 'Char Kalekhan', 'Kazirchar', 'Muladi', 'Nazirpur', 'Safipur'],
                    'Wazirpur': ['Wazirpur Pouroshova', 'Bara Jalia', 'Guthia', 'Harta', 'Satla', 'Shikarpur']
                }
            },
            'Bhola': {
                upazilas: {
                    'Bhola Sadar': ['Bhola Pouroshova', 'Bheduria', 'Dhania', 'Illisha', 'Kachia', 'Shibpur'],
                    'Burhanuddin': ['Burhanuddin Pouroshova', 'Gangapur', 'Kutba', 'Pakshia', 'Sachra', 'Tabgi'],
                    'Charfasson': ['Charfasson Pouroshova', 'Aslampur', 'Char Manika', 'Dhalchar', 'Ewajpur', 'Nurabad'],
                    'Daulatkhan': ['Daulatkhan Pouroshova', 'Char Khalifa', 'Darun', 'Hajipur', 'Madanpur', 'Saidpur'],
                    'Lalmohan': ['Lalmohan Pouroshova', 'Badarpur', 'Kalma', 'Lalmohan', 'Paschim Char Umed', 'Ramagonj'],
                    'Manpura': ['Manpura Pouroshova', 'Dakhshin Sakuchia', 'Hajirhat', 'Manpura', 'Uttar Sakuchia'],
                    'Tazumuddin': ['Tazumuddin Pouroshova', 'Chandpur', 'Char Kalmi', 'Shambhupur', 'Sonapur']
                }
            },
            'Patuakhali': {
                upazilas: {
                    'Patuakhali Sadar': ['Patuakhali Pouroshova', 'Auliapur', 'Badarpur', 'Jainkathi', 'Kalikapur', 'Marichbunia'],
                    'Bauphal': ['Bauphal Pouroshova', 'Adabaria', 'Daspara', 'Kalisuri', 'Nawmala', 'Surjamoni'],
                    'Dashmina': ['Dashmina Pouroshova', 'Alipur', 'Betagi Sankipur', 'Dashmina', 'Rangabali', 'Tikikata'],
                    'Dumki': ['Dumki Pouroshova', 'Angaria', 'Lebukhali', 'Muradia', 'Pangasia', 'Sreerampur'],
                    'Galachipa': ['Galachipa Pouroshova', 'Amkhola', 'Char Biswas', 'Dakua', 'Galachipa', 'Panpatty'],
                    'Kalapara': ['Kalapara Pouroshova', 'Chakamaiya', 'Dhankhali', 'Khaprabhanga', 'Lalua', 'Mithaganj'],
                    'Mirzaganj': ['Mirzaganj Pouroshova', 'Amragachia', 'Deuli Subidkhali', 'Madarbunia', 'Mirzaganj'],
                    'Rangabali': ['Rangabali Pouroshova', 'Bara Baishdia', 'Char Montaz', 'Chhota Baishdia', 'Rangabali']
                }
            },
            'Pirojpur': {
                upazilas: {
                    'Pirojpur Sadar': ['Pirojpur Pouroshova', 'Durgapur', 'Kadamtala', 'Sikder Mallik', 'Tona', 'Vitabaria'],
                    'Bhandaria': ['Bhandaria Pouroshova', 'Bhitabaria', 'Dhanisafa', 'Ikri', 'Nudmulla', 'Telikhali'],
                    'Kawkhali': ['Kawkhali Pouroshova', 'Amrajuri', 'Chirapara', 'Kawkhali', 'Shialkhati'],
                    'Mathbaria': ['Mathbaria Pouroshova', 'Amragachia', 'Daudkhali', 'Dhanisafa', 'Tushkhali', 'Zianagar'],
                    'Nazirpur': ['Nazirpur Pouroshova', 'Nazirpur', 'Sriramkathi', 'Shakharikathi', 'Malikhali'],
                    'Nesarabad': ['Nesarabad Pouroshova', 'Atghorkuriana', 'Balipara', 'Mativangga', 'Shankorpasa'],
                    'Indurkani': ['Indurkani Pouroshova', 'Deulbari Dobra', 'Guarekha', 'Parerhat', 'Shikder Mallik']
                }
            },
            'Barguna': {
                upazilas: {
                    'Barguna Sadar': ['Barguna Pouroshova', 'Aylapatakata', 'Badarkhali', 'Burir Char', 'Dhalua', 'Naltona'],
                    'Amtali': ['Amtali Pouroshova', 'Amtali', 'Arpangashia', 'Gulishakhali', 'Haldia', 'Kukua'],
                    'Bamna': ['Bamna Pouroshova', 'Bamna', 'Bukabunia', 'Dauatala', 'Ramna', 'Ruhita'],
                    'Betagi': ['Betagi Pouroshova', 'Bibichini', 'Bura Mazumdar', 'Hosnabad', 'Kazirabad', 'Mokamia'],
                    'Patharghata': ['Patharghata Pouroshova', 'Char Duanti', 'Kakchira', 'Kalmegha', 'Patharghata', 'Raihanpur'],
                    'Taltali': ['Taltali Pouroshova', 'Chhota Bagi', 'Nishanbaria', 'Sarikkhali', 'Taltali']
                }
            },
            'Jhalokati': {
                upazilas: {
                    'Jhalokati Sadar': ['Jhalokati Pouroshova', 'Gabha Ramchandrapur', 'Keora', 'Nabagram', 'Nathullabad', 'Shekherhat'],
                    'Kathalia': ['Kathalia Pouroshova', 'Amua', 'Niamatpur', 'Panchkaran', 'Shoulajalia'],
                    'Nalchity': ['Nalchity Pouroshova', 'Binoykati', 'Dapdapia', 'Kirtipasha', 'Mollahat', 'Subrahmanya'],
                    'Rajapur': ['Rajapur Pouroshova', 'Baruipara', 'Mathbari', 'Rajapur', 'Saturia', 'Sekherhat']
                }
            }
        }
    }
}; 