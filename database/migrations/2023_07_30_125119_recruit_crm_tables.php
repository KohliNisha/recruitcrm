<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecruitCrmTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION FOREIGN_KEY_CHECKS=0;');

        //Table structure for table `user_t`
        DB::statement('CREATE TABLE `user_t` (
            `id` varchar(256) NOT NULL,
            `email` varchar(256) NOT NULL,
            `password` varchar(256) NOT NULL,
            `first_name` varchar(256) NOT NULL,
            `last_name` varchar(256) NOT NULL
          )');

        //Indexes for table `user_t`
        DB::statement('ALTER TABLE `user_t` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);');




        //Table structure for table `currency_t`
        DB::statement(' CREATE TABLE `currency_t` (
            `id` varchar(256) NOT NULL,
            `code` varchar(256) NOT NULL
          ) ');

        //Indexes for table `currency_t`
        DB::statement('ALTER TABLE `currency_t` ADD PRIMARY KEY (`id`);');

        //Inserting some dummy data in currency_t table.
        DB::statement("INSERT INTO `currency_t` (`id`, `code`) VALUES ('0182f626-8706-4ee4-a31d-fa51289be404', 'INR'), ('0182f626-8706-4ee4-a31d-fa51289be905', 'USD');");

        


        //Table structure for table `address_t`
        DB::statement('CREATE TABLE `address_t` (
            `id` varchar(256) NOT NULL,
            `country` varchar(256) NOT NULL,
            `street_address` varchar(256) NOT NULL,
            `city` varchar(256) NOT NULL,
            `state` varchar(256) NOT NULL,
            `postal_code` varchar(256) NOT NULL
          )');

        //Indexes for table `address_t`
        DB::statement('ALTER TABLE `address_t` ADD PRIMARY KEY (`id`);');

        //Inserting some dummy data in address_t table...
        DB::statement("INSERT INTO `address_t` (`id`, `country`, `street_address`, `city`, `state`, `postal_code`) VALUES ('0182f626-8706-4ee4-a31d-fa51289be804', 'India', 'H-111', 'Noida', 'U.P.', '201306'), ('0182f626-8706-4ee4-a31d-fa51289be805', 'India', 'H-104', 'Noida', 'U.P.', '201306');");
        
        


        //Table structure for table `candidate_t`
        DB::statement('CREATE TABLE `candidate_t` (
            `id` varchar(256) NOT NULL,
            `owner_id` varchar(256) NOT NULL,
            `first_name` varchar(256) NOT NULL,
            `last_name` varchar(256) NOT NULL,
            `age` int(11) NOT NULL,
            `department` varchar(256) NOT NULL,
            `min_salary_expectation` int(11) NOT NULL,
            `max_salary_expectation` int(11) NOT NULL,
            `currency_id` varchar(256) NOT NULL,
            `address_id` varchar(256) NOT NULL
          )');

        //Indexes for table `candidate_t`
        DB::statement('ALTER TABLE `candidate_t`
            ADD PRIMARY KEY (`id`),
            ADD KEY `owner_id` (`owner_id`),
            ADD KEY `currency_id` (`currency_id`),
            ADD KEY `address_id` (`address_id`);');

        //Constraints for table `candidate_t`
        DB::statement('ALTER TABLE `candidate_t`
            ADD CONSTRAINT `candidate_t_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user_t` (`id`),
            ADD CONSTRAINT `candidate_t_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currency_t` (`id`),
            ADD CONSTRAINT `candidate_t_ibfk_3` FOREIGN KEY (`address_id`) REFERENCES `address_t` (`id`);');




        //Table structure for table `phone_number_t`
        DB::statement('CREATE TABLE `phone_number_t` (
            `id` varchar(256) NOT NULL,
            `candidate_id` varchar(256) NOT NULL,
            `type` varchar(256) NOT NULL,
            `number` varchar(256) NOT NULL
          )');

        //Indexes for table `phone_number_t`
        DB::statement('ALTER TABLE `phone_number_t`
        ADD PRIMARY KEY (`id`),
        ADD KEY `candidate_id` (`candidate_id`);');

        //Constraints for table `phone_number_t`
        DB::statement('ALTER TABLE `phone_number_t`
        ADD CONSTRAINT `phone_number_t_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_t` (`id`);');


        

        //Table structure for table `education_t`
        DB::statement('CREATE TABLE `education_t` (
            `id` varchar(256) NOT NULL,
            `candidate_id` varchar(256) NOT NULL,
            `school` varchar(256) NOT NULL,
            `degree` varchar(256) NOT NULL,
            `major` varchar(256) NOT NULL
          )');

        //Indexes for table `education_t`
        DB::statement('ALTER TABLE `education_t`
        ADD PRIMARY KEY (`id`),
        ADD KEY `candidate_id` (`candidate_id`);');

        //Constraints for table `education_t`
        DB::statement('ALTER TABLE `education_t`
        ADD CONSTRAINT `education_t_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_t` (`id`);');




        //Table structure for table `skill_t`
        DB::statement('CREATE TABLE `skill_t` (
            `id` varchar(256) NOT NULL,
            `candidate_id` varchar(256) NOT NULL,
            `skill` varchar(256) NOT NULL,
            `level` varchar(256) NOT NULL
          )');
        
        //Indexes for table `skill_t`
        DB::statement('ALTER TABLE `skill_t`
        ADD PRIMARY KEY (`id`),
        ADD KEY `candidate_id` (`candidate_id`);');

        //Constraints for table `skill_t`
        DB::statement('ALTER TABLE `skill_t`
        ADD CONSTRAINT `skill_t_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_t` (`id`);');




        //Table structure for table `experience_t`
        DB::statement('CREATE TABLE `experience_t` (
            `id` varchar(256) NOT NULL,
            `candidate_id` varchar(256) NOT NULL,
            `company` varchar(256) NOT NULL,
            `title` varchar(256) NOT NULL,
            `years` varchar(256) NOT NULL
          )');

        //Indexes for table `experience_t`
        DB::statement('ALTER TABLE `experience_t`
        ADD PRIMARY KEY (`id`),
        ADD KEY `candidate_id` (`candidate_id`);');

        //Constraints for table `experience_t`
        DB::statement('ALTER TABLE `experience_t`
        ADD CONSTRAINT `experience_t_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_t` (`id`);');


        DB::statement('SET SESSION FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET SESSION FOREIGN_KEY_CHECKS=0;');

        //Droping experience_t table
        DB::statement('DROP TABLE IF EXISTS `experience_t`');


        //Droping skill_t table
        DB::statement('DROP TABLE IF EXISTS `skill_t`');


        //Droping education_t table
        DB::statement('DROP TABLE IF EXISTS `education_t`');
        
        
        //Droping phone_number_t table
        DB::statement('DROP TABLE IF EXISTS `phone_number_t`');
        
        
        //Droping candidate_t table
        DB::statement('DROP TABLE IF EXISTS `candidate_t`');
        
        
        //Droping address_t table
        DB::statement('DROP TABLE IF EXISTS `address_t`');
        
        
        //Droping currency_t table
        DB::statement('DROP TABLE IF EXISTS `currency_t`');


        //Droping user_t table
        DB::statement('DROP TABLE IF EXISTS `user_t`');


        DB::statement('SET SESSION FOREIGN_KEY_CHECKS=1;');
    }
}
