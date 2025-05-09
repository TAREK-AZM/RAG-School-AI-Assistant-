import pika
import json
import os
from datetime import datetime
from fastapi import FastAPI
from contextlib import asynccontextmanager
import threading
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Configuration
STORAGE_DIR = "uploads"
os.makedirs(STORAGE_DIR, exist_ok=True)




def save_binary_file(body, properties, headers):
    """Save binary file content directly"""
    try:
        # Extract metadata from headers
        metadata = {}
        
        # Check for custom headers
        if headers and isinstance(headers, dict):
            file_name = headers.get('x-file-name')
            provider = headers.get('x-provider', 'unknown')
            mime_type = headers.get('x-mime-type', 'application/octet-stream')
            
            # Try to get full metadata if available
            if 'x-metadata' in headers:
                try:
                    metadata = json.loads(headers['x-metadata'])
                except:
                    logger.warning("Could not parse x-metadata header")
                    
        # Fallbacks if headers aren't available
        if not file_name:
            file_name = metadata.get('file_name', 'unknown_file')
        if not provider:
            provider = metadata.get('provider', 'unknown')
        if not mime_type:
            mime_type = metadata.get('mime_type', 'application/octet-stream')
        
        # Get content type from properties if available
        if properties and hasattr(properties, 'content_type') and properties.content_type:
            mime_type = properties.content_type
            
        # Generate filename with timestamp
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        save_path = os.path.join(STORAGE_DIR, f"{timestamp}_{file_name}")
        
        # Log what we're saving
        logger.info(f"Saving binary file: {file_name}")
        logger.info(f"Provider: {provider}")
        logger.info(f"MIME type: {mime_type}")
        logger.info(f"Content size: {len(body)} bytes")
        
        # Save the file directly (body is already binary)
        with open(save_path, "wb") as f:
            f.write(body)
            
        logger.info(f"Successfully saved file to: {save_path}")
        return True
        
    except Exception as e:
        logger.error(f"Failed to save binary file: {str(e)}")
        import traceback
        logger.error(traceback.format_exc())
        return False

def start_consumer():
    """RabbitMQ consumer for binary content"""
    try:
        connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host='127.0.0.1',
                port=5672,
                heartbeat=600,
                credentials=pika.PlainCredentials(
                    username='guest',
                    password='guest'
                )
            )
        )
        channel = connection.channel()
        
        channel.queue_declare(
            queue='file_storage',
            durable=True
        )
        
        def callback(ch, method, properties, body):
            try:
                logger.info(f"Received message of size: {len(body)} bytes")
                
                # Extract headers from properties
                headers = {}
                if properties.headers:
                    headers = properties.headers
                    logger.info(f"Message headers: {list(headers.keys())}")
                
                # Save the binary content directly
                if save_binary_file(body, properties, headers):
                    ch.basic_ack(delivery_tag=method.delivery_tag)
                else:
                    logger.error("Failed to save file, sending negative acknowledgment")
                    ch.basic_nack(delivery_tag=method.delivery_tag, requeue=False)
                    
            except Exception as e:
                logger.error(f"Error processing message: {str(e)}")
                ch.basic_nack(delivery_tag=method.delivery_tag, requeue=False)
                import traceback
                logger.error(traceback.format_exc())
        
        channel.basic_qos(prefetch_count=1)
        channel.basic_consume(
            queue='file_storage',
            on_message_callback=callback,
            auto_ack=False
        )
        
        logger.info(" [*] Binary file storage consumer started")
        channel.start_consuming()
        
    except Exception as e:
        logger.error(f"RabbitMQ connection failed: {str(e)}")
        import traceback
        logger.error(traceback.format_exc())

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Modern lifespan handler (replaces @app.on_event)"""
    # Start consumer thread
    thread = threading.Thread(target=start_consumer, daemon=True)
    thread.start()
    logger.info("Application startup complete")
    yield
    logger.info("Application shutdown")

app = FastAPI(lifespan=lifespan)

@app.get("/")
def health_check():
    return {"status": "Binary file receiver is running"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)