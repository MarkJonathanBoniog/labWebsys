<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //not connected to records table, more of like a masterlist that can be expanded
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("abbrev");
            $table->timestamps();
        });
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->boolean("hasOtr"); //confirmation if they have their otr before they can formally start a transaction record
            $table->boolean("hasForm"); //confirmation if they have their form 137 before they can formally start a transaction record
            $table->string("number"); //the user's number in the transaction list relative per year and per program, for example number of bsit students is different to the sequencing of number under bsmath students, also the same logic to records made under year 2025 and 2024, eg. 147
            $table->string("refnumber"); //has a format of (Program)-()-(year when this record is availed) eg. BSIT-147-2025
            $table->enum('status', ['Pending', 'Ready', 'Completed', 'Failed']); //Pending-record still in process, Ready-record is done but isn't claimed yet, Completed-record was claimed by the requestor, Failed-record dropped and rendered inactive
            $table->string("fname")->index();
            $table->string("mname");
            $table->string("lname")->index();
            $table->string("sex")->nullable(); //male or female
            $table->string("semester")->nullable(); //1st or 2nd
            $table->string("schoolyear")->nullable(); //eg. 2025-2026
            $table->string("program")->index();//will not be related to programs in code, but what's in the program table will be added in here
            $table->string("transferfrom")->nullable(); //if the student is transferring into our school, where did they transferred from
            $table->string("transferto")->nullable(); //if the student is transferring out of our school, where are they transferring to
            $table->boolean("isUndergrad");
            //the follwing field are for time details
            $table->year("yearGraduated")->nullable(); //the year when the student has graduated when they are not undergrad
            $table->year("year"); //the year when the student is availing the record
            $table->timestamp("claimed")->nullable(); //the time when the student has claimed the record
            $table->timestamps(); //this'll serve as the time it is made and the time it is updated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
        Schema::dropIfExists('programs');
    }
};
