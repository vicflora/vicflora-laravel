<?php

namespace App\Http\Controllers;

use Exception;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }

    public function ping()
    {
        // create a ping query
        $ping = $this->client->createPing();

        // execute the ping query
        try {
            $this->client->ping($ping);
            return response()->json('Looks like I got it to work');
        } catch (\Solarium\Exception\HttpException $e) {
            echo 'Solarium ran into a problem:<br/><br/>';
            echo $e->getMessage();
        } catch (Exception $e) {
            echo 'Something else went wrong:<br/><br/>';
            echo $e->getMessage();
        }    
    }

    public function search()
    {
        $query = $this->client->createSelect();
        $query->addFilterQuery(array('key'=>'scientific_name', 'query'=>'scientific_name:Acacia', 'tag'=>'include'));
        $resultset = $this->client->select($query);

        // display the total number of documents found by solr
        echo 'NumFound: ' . $resultset->getNumFound();

        // show documents using the resultset iterator
        foreach ($resultset as $document) {

            echo '<hr/><table>';

            // the documents are also iterable, to get all fields
            foreach ($document as $field => $value) {
                // this converts multivalue fields to a comma-separated string
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
            }

            echo '</table>';
        }
    }

}