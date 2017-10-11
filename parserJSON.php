<?php
namespace AcademMedia\Test\Parser;
//.................................................................
class Model
{   
    public function jsonToObj($json)
    {    
        function cmp($a,$b) //функция сравнения для usort
        {
            if($a->data == $b->data){
                return 0;
            }
            return ($a->data > $b->data) ? -1 : 1;
        }
        $obj = json_decode($json);
        foreach($obj->events as $event){
            $event->data = str_replace("{\"time_on\":", "timeOn=",$event->data);
            $event->data = str_replace(",\"type\":", "&type=",$event->data);
            $event->data = str_replace("}", "", $event->data);
            $event->data = parse_str($event->data, $output);
            $event->data = $output['timeOn'];
            $event->type = $output['type'];            
            $event->event = strtolower($event->event);           
            $event->timestamp = date("Y-m-d H:i",intval($event->timestamp));
        }
        usort ($obj->events,"AcademMedia\Test\Parser\cmp");
        return $obj;
    }
}

class View
{
    private function makeBLine($event)
    { 
        echo "<tr>"; 
        foreach($event as $info){
            echo "<td><b>", "$info", "</b></td>";
        } 
        echo "</tr>"; 
    } 
    
    private function makeILine($event)
    { 
        echo "<tr>"; 
        foreach($event as $info){
           echo "<td><i>", "$info", "</i></td>"; 
        } 
        echo "</tr>"; 
    } 

    private function makeLine($event)
    { 
        echo "<tr>"; 
        foreach($event as $info){
            echo "<td>", "$info", "</td>";  
        } 
        echo "</tr>"; 
    } 
            
    public function makeView($obj)
    {
        echo '<h4>';
        echo $obj->application_name,', ';
        echo $obj->country,', ';
        echo $obj->city,', ';               //отрисовка заголовка
        echo $obj->app_id,' ';
        echo '</h4>';
        $zagolovok = array('event', 'date', 'time_on', 'type');
    
        echo "<table border = 1>";
        echo $this->makeLine($zagolovok); //отрисовка загаловка таблицы       
        foreach ($obj->events as $event){ //отрисовка тела таблицы
            if($event->data > 0){
                echo $this->makeILine($event); 
            }
            elseif($event->type > 0){
                echo $this->makeBLine($event);
            }
            else 
                echo $this->makeLine($event);
            }
        echo "</table>";
    }
        
}

class Controller
{
    public function getView(){
        date_default_timezone_set('Asia/Novosibirsk');    
        $model = new Model;
        $view = new View;
        $view->makeView($model->jsonToObj(file_get_contents("application.json")));
    }
} 

$A = new Controller;
$A->getView();
