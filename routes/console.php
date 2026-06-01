<?php

use Illuminate\Support\Facades\Schedule;

// Recalculate course recommendations & cleanup orphan files weekly (example hook).
Schedule::command('storage:link')->weekly();
