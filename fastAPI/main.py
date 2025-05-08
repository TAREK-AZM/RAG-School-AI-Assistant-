import pika
import json
import base64
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


# def save_file(message: dict):
#     """Save received file to disk"""
#     try:
#         file_content =message['file_content']
#         timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
#         save_path = os.path.join(STORAGE_DIR, f"{timestamp}_{message['Document_id']}")
        
#         with open(save_path, "wb") as f:
#             f.write(file_content)
        
#         logger.info(f"Saved file: {save_path}")
#         return True
        
#     except Exception as e:
#         logger.error(f"Failed to save file: {str(e)}")
#         return False

def save_file(message: dict):
    STORAGE_DIR = "uploads"
    os.makedirs(STORAGE_DIR, exist_ok=True)
 
    """Save received file to disk"""
    try:
        # Check for required fields
        if 'file_content' not in message:
            logger.error("Missing file_content in message")
            return False
            
        # Get filename from message, with fallbacks
        file_name = message.get('file_name', 'unknown_file')
        
        # Debug log to see what we're receiving
        logger.info(f"Message keys: {list(message.keys())}")
        logger.info(f"File content type: {type(message['file_content'])}")
        
        try:
            # Try to decode if the content is base64 encoded
            if isinstance(message['file_content'], str):
                file_content = base64.b64decode(message['file_content'])
            else:
                # If it's already bytes, use it directly
                file_content = message['file_content']
                
            # If we got here without an exception, we have valid bytes
            logger.info(f"Successfully processed file content of size: {len(file_content)} bytes")
        except Exception as e:
            logger.error(f"Error decoding file content: {str(e)}")
            return False
        
        # Generate filename with timestamp
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        save_path = os.path.join(STORAGE_DIR, f"{timestamp}_{file_name}")
        
        # Save the file
        with open(save_path, "wb") as f:
            f.write(file_content)
        
        provider = message.get('provider', 'unknown')
        logger.info(f"Saved file: {save_path} from provider: {provider}")
        return True
        
    except Exception as e:
        logger.error(f"Failed to save file: {str(e)}")
        # Add more detailed error logging
        import traceback
        logger.error(traceback.format_exc())
        return False

def start_consumer():
    """RabbitMQ consumer in background"""
    try:
        connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host='127.0.0.1',  # Changed from 'rabbitmq' to 'localhost'
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
                message = json.loads(body)
                if save_file(message):
                    ch.basic_ack(delivery_tag=method.delivery_tag)
            except Exception as e:
                logger.error(f"Error processing message: {str(e)}")
        
        channel.basic_qos(prefetch_count=1)
        channel.basic_consume(
            queue='file_storage',
            on_message_callback=callback,
            auto_ack=False
        )
        
        logger.info(" [*] File storage consumer started")
        channel.start_consuming()
        
    except Exception as e:
        logger.error(f"RabbitMQ connection failed: {str(e)}")

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
    return {"status": "File receiver is running"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)