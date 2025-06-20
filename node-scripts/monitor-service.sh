#!/bin/bash

# Enhanced WhatsApp Service Monitor Script
# This script monitors the Enhanced Handler service and restarts it if needed

LOG_FILE="/Applications/MAMP/htdocs/blazt/node-scripts/monitor.log"
SERVICE_URL="http://localhost:3001/health"
MAX_RETRIES=3
RETRY_COUNT=0

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

check_service() {
    local response
    response=$(curl -s --max-time 5 "$SERVICE_URL" 2>/dev/null)
    
    if [[ $? -eq 0 ]] && [[ $(echo "$response" | jq -r '.status' 2>/dev/null) == "OK" ]]; then
        return 0
    else
        return 1
    fi
}

restart_service() {
    log "Restarting Enhanced Handler service..."
    
    # Kill existing processes
    pkill -f "server-enhanced.js"
    sleep 2
    
    # Start new process
    cd /Applications/MAMP/htdocs/blazt/node-scripts
    nohup node server-enhanced.js > enhanced-handler.log 2>&1 &
    
    # Wait for service to start
    sleep 5
    
    if check_service; then
        log "Enhanced Handler service restarted successfully"
        RETRY_COUNT=0
        return 0
    else
        log "Failed to restart Enhanced Handler service"
        return 1
    fi
}

# Main monitoring loop
log "Starting Enhanced Handler service monitor"

while true; do
    if check_service; then
        if [[ $RETRY_COUNT -gt 0 ]]; then
            log "Enhanced Handler service is healthy again (after $RETRY_COUNT retries)"
            RETRY_COUNT=0
        fi
        
        # Service is healthy, sleep for 30 seconds
        sleep 30
    else
        RETRY_COUNT=$((RETRY_COUNT + 1))
        log "Enhanced Handler service is unhealthy (attempt $RETRY_COUNT/$MAX_RETRIES)"
        
        if [[ $RETRY_COUNT -ge $MAX_RETRIES ]]; then
            if restart_service; then
                log "Service monitoring resumed"
            else
                log "Failed to restart service after $MAX_RETRIES attempts. Manual intervention required."
                exit 1
            fi
        else
            # Wait before next retry
            sleep 10
        fi
    fi
done
