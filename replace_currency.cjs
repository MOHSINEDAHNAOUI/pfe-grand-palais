const fs = require('fs');
const path = require('path');

function walk(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    const dirPath = path.join(dir, f);
    const isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walk(dirPath, callback) : callback(dirPath);
  });
}

function replaceInFile(filePath) {
  if (!filePath.endsWith('.vue') && !filePath.endsWith('.js')) return;

  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;

  // Replace symbol € with DH
  content = content.replace(/€/g, 'DH');

  // Replace 'EUR' with 'MAD'
  content = content.replace(/'EUR'/g, "'MAD'");
  content = content.replace(/"EUR"/g, '"MAD"');
  
  // Also 'fr-FR' in Intl.NumberFormat can be changed to 'fr-MA' for MAD
  content = content.replace(/'fr-FR', { style: 'currency', currency: 'MAD'/g, "'fr-MA', { style: 'currency', currency: 'MAD'");
  
  // Specific occurrences of text 'EUR' that might be plain text in UI:
  // e.g. <span ...>EUR</span>
  content = content.replace(/>EUR</g, '>MAD<');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
  }
}

walk(path.join(__dirname, 'frontend/src'), replaceInFile);
console.log('Replacement complete.');
