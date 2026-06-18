const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'resources', 'views', 'dashboard', 'campaign_report.blade.php');
const content = fs.readFileSync(filePath, 'utf8');

// Find the <script> block content
const scriptMatch = content.match(/<script>([\s\S]*?)<\/script>/);
if (!scriptMatch) {
    console.log("No script tag found!");
    process.exit(1);
}

const scriptContent = scriptMatch[1];

// Try parsing it using Node vm module
const vm = require('vm');
try {
    new vm.Script(scriptContent);
    console.log("JavaScript is syntactically CORRECT!");
} catch (err) {
    console.error("JavaScript syntax ERROR:");
    console.error(err);
    process.exit(1);
}
