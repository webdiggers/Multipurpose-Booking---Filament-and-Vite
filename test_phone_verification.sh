#!/bin/bash

# Test Phone Verification Flow
echo "Testing Phone Verification Flow..."
echo ""

# Step 1: Send verification code
echo "Step 1: Sending verification code for phone 9999999999"
RESPONSE=$(curl -s -X POST http://localhost:8000/livewire/update \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Livewire: true" \
  -c cookies.txt \
  --data '{"snapshot":"{\"data\":{\"phone\":\"\",\"verificationCode\":\"\",\"isCodeSent\":false},\"memo\":{\"name\":\"phone-verification\",\"path\":\"verify-phone\",\"method\":\"GET\",\"children\":[],\"scripts\":[],\"assets\":[],\"errors\":[],\"locale\":\"en\"}}","updates":[{"type":"callMethod","payload":{"id":"abc123","method":"sendCode","params":[]}}]}')

echo "Response: $RESPONSE"
echo ""

# Extract OTP from Laravel logs
echo "Step 2: Getting OTP from logs..."
OTP=$(tail -n 20 /Users/amarsingh/Sites/officework/smarters_studio/studio-booking/storage/logs/laravel.log | grep "OTP for" | tail -1 | grep -o '[0-9]\{4\}' | tail -1)
echo "OTP Found: $OTP"
echo ""

echo "Test complete!"
echo ""
echo "Manual steps:"
echo "1. Visit http://localhost:8000/verify-phone"
echo "2. Enter phone: 9999999999"
echo "3. Click 'Send Verification Code'"
echo "4. Look for OTP in the flash message"
echo "5. Enter the OTP and click 'Verify Code'"
