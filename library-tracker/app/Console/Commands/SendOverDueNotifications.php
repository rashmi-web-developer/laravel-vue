<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendOverDueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:send-over-due-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue loan notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $now = Carbon::now();
        $overDueLoans = Loan::whereNull('returned_at')->where('due_at', '<', $now)->get();
        foreach ($overDueLoans as $loan) {
            Log::info("Overdue loan User: {$loan->user_id} - Book {$loan->book_id}, Due at: {$loan->due_at}");
        }
        $count = $overDueLoans->count();
        Log::info("Total overdue loans: {$count}");
        return 0;
    }
}
