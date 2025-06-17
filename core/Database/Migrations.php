<?php

namespace core\Database;

interface Migrations
{
    public function up();

    public function down();
}