<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Tables:\n";
foreach (DB::select("SELECT name FROM sqlite_master WHERE type='table'") as $table) {
    echo " - " . $table->name . "\n";
}

echo "\nCampaign sums:\n";
print_r(DB::table('campaigns')->selectRaw('count(id) as count, sum(potential_reach) as reach, sum(total_shares) as shares, sum(total_clicks) as clicks, sum(total_comments) as comments, sum(total_likes) as likes, sum(total_posts) as posts, sum(form_submissions) as forms')->first());

echo "\nRemovedCampaign sums:\n";
print_r(DB::table('removed_campaigns')->selectRaw('count(id) as count, sum(potential_reach) as reach, sum(total_shares) as shares, sum(total_clicks) as clicks, sum(total_comments) as comments, sum(total_likes) as likes, sum(total_posts) as posts, sum(registrations) as forms, sum(roi) as roi')->first());

echo "\nClient sums:\n";
print_r(DB::table('clients')->selectRaw('count(id) as count, sum(consumed) as consumed, sum(remaining) as remaining, sum(clicks) as clicks, sum(posts) as posts, sum(comments) as comments')->first());

echo "\nClientSummary sums:\n";
print_r(DB::table('client_summaries')->selectRaw('count(client_name) as count, sum(campaigns_count) as campaigns, sum(shares_count) as shares, sum(form_submissions_count) as forms, sum(credits_consumed) as credits')->first());
