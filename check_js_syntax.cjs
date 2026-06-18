const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'resources', 'views', 'dashboard', 'campaign_report.blade.php');
const content = fs.readFileSync(filePath, 'utf8');

// Find all script blocks and test the main one
const scriptMatches = [...content.matchAll(/<script>([\s\S]*?)<\/script>/g)];
if (scriptMatches.length === 0) {
    console.log("No script tag found!");
    process.exit(1);
}

const vm = require('vm');
let count = 0;
for (const match of scriptMatches) {
    count++;
    const scriptContent = match[1];
    
    // We ignore script blocks that have blade templates like @json or @else, because vm.Script doesn't understand blade syntax
    // Let's replace simple blade markers like @json(...) or @if ... @endif with mock JS code before validating syntax
    const cleanedScript = scriptContent
        .replace(/@json\(.*?\)/g, '[]')
        .replace(/@php[\s\S]*?@endphp/g, '')
        .replace(/@\w+[\s\S]*?/g, ''); // strip other @ directives

    try {
        new vm.Script(cleanedScript);
        console.log(`Script block ${count} is syntactically CORRECT!`);
    } catch (err) {
        console.error(`Script block ${count} syntax ERROR:`);
        console.error(err);
        process.exit(1);
    }
}
