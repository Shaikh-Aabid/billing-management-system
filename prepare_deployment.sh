#!/bin/bash

echo "🚀 Starting Deployment Preparation..."

# 1. Build Assets
echo "📦 Building Frontend Assets..."
npm run build

# 2. Optimize Autoloader (Optional - usually done on server, but safe to do here)
# echo "🔧 Optimizing Composer..."
# composer install --optimize-autoloader --no-dev

# 3. Create Zip
echo "🤐 Zipping Project..."
zip -r billing-software_deploy.zip . \
    -x "node_modules/*" \
    -x ".git/*" \
    -x "tests/*" \
    -x "storage/logs/*" \
    -x "storage/framework/cache/*" \
    -x "storage/framework/views/*" \
    -x "storage/framework/sessions/*" \
    -x "billing-software_deploy.zip"

echo "✅ Deployment Package Created: billing-software_deploy.zip"
echo "📝 Next Steps:"
echo "1. Upload 'billing-software_deploy.zip' to your Hostinger subdomain directory."
echo "2. Check 'deployment_guide.md' for full instructions."
