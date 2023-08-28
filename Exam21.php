<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Exam21 extends Page
{

    protected function __construct()
    {
        parent::__construct();
    }


    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $sqlGet = "SELECT * FROM games";

        $recordSet = $this->_database->query($sqlGet);

        if(!$recordSet) {
            throw new Exception("Datenbankfehler: ".$this->_database->error);
        }

        $result = array();


        $record = $recordSet->fetch_assoc();

        while ($record) {
            $result[] = $record;
            $record = $recordSet->fetch_assoc();
        }

        return $result;
    }

    protected function generateView():void
    {
        $this->generatePageHeader("Spielplanung");
        $data = $this->getViewData();

//        var_dump($data);
        echo <<< EOT
        <body onload='polldata()'>
        <header>
        <img src="Logo.png" alt="Logo" height="30em">
        <h1>Spielplanung</h1>
        </header>
        <section>
        <form method="post" accept-charset="utf-8" action="Exam21.php">
EOT;
        $gefundenNichtAbgeschlossen = false;
        foreach ($data as $item) {
            if($item["status"] == 1) {
                echo "<input type='text' name='spielId' value={$item["id"]} id='spielId' hidden>";
                $gefundenNichtAbgeschlossen = true;
                echo "<h3>{$item['datetime']} Uhr gegen {$item['opposingTeam']}</h3>";
                break;
            }
        }
        $gefundenAbgeschlossen = false;

        if($gefundenNichtAbgeschlossen === false) {
            foreach ($data as $item) {
                if($item["status"] == 2) {
                    $gefundenAbgeschlossen = true;
                    echo "<h3>{$item['datetime']} Uhr gegen {$item['opposingTeam']}</h3>";
                    break;
                }
            }
        }

        if($gefundenAbgeschlossen === false && $gefundenNichtAbgeschlossen === false) {
            echo "<h3>kein aktuelles Spiel</h3>";
        }

        echo <<< EOT
        <h4 id="zusagen">Zusagen Spielerinnen: </h4>
        
        <input type="submit" value="Planung abschließen" name="ready">
        </form>
        </section>
        <section>
        <h4>Spiele</h4>
        <table>
        <tr>
        <th>Datum</th>
        <th>Team</th>
        <th>Status</th>
        </tr>
EOT;
        foreach ($data as $item) {
            echo <<< EOT
        <tr>
        <td>{$item['datetime']}</td>
        <td>{$item['opposingTeam']}</td>
EOT;
            if($item['status'] == 0) {
                echo "<td>zukunftiges Spiel</td>";
            }
            if($item['status'] == 1) {
                echo "<td>in Planung</td>";
            }
            if($item['status'] == 2) {
                echo "<td>Planung abgeschlossen</td>";
            }
            if($item['status'] == 3) {
                echo "<td>vorbei</td>";
            }
        echo "</tr>";
        }
        echo <<< EOT
        </table>
        </section>
        </body>
EOT;

        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_POST["ready"]) && isset($_POST["spielId"])) {
            $spielID = isset($_POST["spielId"]);
            $sqlUpdate = "UPDATE games SET status = 2 WHERE id = $spielID";

            $sqlCheck = $this->_database->query($sqlUpdate);

            if(!$sqlCheck) {
                throw new Exception("Datenbankfehler Update: ".$this->_database->error);
            }
        }
    }

    public static function main():void
    {
        try {
            $page = new Exam21();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}


Exam21::main();

