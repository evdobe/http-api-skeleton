<?php declare(strict_types=1);

namespace Infrastructure\Event\Adapter\Postgres;

use Application\Event\Projector;
use Application\Event\Store;
use Application\Event\StoreListener as EventStoreListener;
use Domain\Event\Event;
use PDO;
use PDOException;

class StoreListener implements EventStoreListener
{

    protected PDO $con;

    protected const EVENT_NOTIFY_PROCEDURE_SQL = "
        CREATE OR REPLACE FUNCTION public.projector_event_notify()
        RETURNS trigger
        AS \$function\$
        BEGIN
            IF NEW.projected = false THEN
                PERFORM pg_notify('projector_event', row_to_json(NEW)::text);
            END IF;
            RETURN NULL;
        END;
        \$function\$
        LANGUAGE plpgsql;
    ";

    protected const EVENT_NOTIFY_TRIGGER_SQL = "
        CREATE TRIGGER trigger_on_event_insert AFTER INSERT ON \"event\"
        FOR EACH ROW EXECUTE PROCEDURE projector_event_notify();
    ";

    protected const LISTEN_TIMEOUT = 60*10000;

    public function __construct()
    {
        $this->con = new PDO("pgsql:host=".getenv('DB_HOST').";dbname=".getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD'));
        $this->setUpListener();
    }

    public function listen(Store $store):void{
        if (!$this->listenerSetUp){
            throw new \Exception('Listener is not set up!');
        }
        echo "Listening for pgsql notifications...\n";
        $notification = $this->con->pgsqlGetNotify(PDO::FETCH_ASSOC, self::LISTEN_TIMEOUT);
        if (!$notification) {
            echo "Timeout with no messages\n";
            return;
        }
        $eventData = json_decode($notification['payload'], true);
        echo "Received notification for event with id = ".$eventData['id']."\n";
        $store->notify($eventData);
    }

    protected function setUpListener(){
        $this->con->exec(SELF::EVENT_NOTIFY_PROCEDURE_SQL);
        try {
            $this->con->exec(SELF::EVENT_NOTIFY_TRIGGER_SQL);
        }
        catch (PDOException $e){
            if ($e->getCode() == '42710'){
                //SQLSTATE[42710]: Duplicate object: 7 ERROR:  trigger "trigger_on_event_insert" for relation "event"
                echo "Trigger already defined: ".$e->getMessage()."\n";
            }
            else  {
                throw $e;
            }    
        }
        $this->con->exec("LISTEN projector_event;");
        $this->listenerSetUp = true;
    }
}
