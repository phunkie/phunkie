#!/bin/bash

# Setup script for development environment
# Installs git hooks and dependencies

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "🔧 Setting up development environment..."

# Install composer dependencies
echo ""
echo "📦 Installing dependencies..."
composer install

# Install pre-commit hook
echo ""
echo "🪝 Installing git hooks..."
cp "$SCRIPT_DIR/pre-commit" "$PROJECT_ROOT/.git/hooks/pre-commit"
chmod +x "$PROJECT_ROOT/.git/hooks/pre-commit"

echo ""
echo "✅ Development environment setup complete!"
echo ""
echo "Available commands:"
echo "  composer test      - Run tests"
echo "  composer lint      - Run code style and static analysis"
echo "  composer cs-fix    - Auto-fix code style issues"
echo "  composer check     - Run lint + tests"
echo "  composer test-all  - Run tests on all PHP versions (requires Docker)"
