<?php

namespace App\GraphQL\Queries;

use App\Models\Image;

class ImageCaption
{
    /**
     * Return a caption for the Image.
     *
     * @param  Image $image Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @return string
     */
    public function __invoke(Image $image, array $args)
    {

        $scientificName = "<i>{$image->acceptedConcept->taxonName->full_name}</i>";
        if ($image->taxon_id != $image->accepted_id) {
            $scientificName .= " (as <i>{$image->taxonConcept->taxonName->full_name}</i>)";
        }
        $search = [' subsp. ', ' var. ', ' f. '];
        $replace = [
            '</i> subsp. <i>',
            '</i> var. <i>',
            '</i> f. <i>'
        ];
        $scientificName = str_replace($search, $replace, $scientificName);

        if (substr($image->license, 0, 5) === 'CC BY') {
            $bits = explode(' ', $image->license);
            $url = 'https://creativecommons.org/licenses/';
            $url .= strtolower($bits[1]);
            $url .= (isset($bits[2])) ? '/' . $bits[2] : '/4.0';
            if (isset($bits[3])) {$url .= '/' .strtolower ($bits[3]);}
            $license = "<a href='$url'>$image->license</a>";
        }
        elseif ($image->license == 'All rights reserved') {
            $license = 'all rights reserved';
        }
        elseif ($image->subjectCategory == 'Flora of the Otway Plain and Ranges Plate') {
            $license = 'not to be reproduced without prior permission from CSIRO Publishing.';
        }
        else {
            $license = "<a href='https://creativecommons.org/licenses/by-nc-sa/4.0'>CC BY-NC-SA 4.0</a>";
        }

        $caption = $scientificName;
        if ($image->caption) {
            $caption .= ". {$image->caption}";
        }
        $caption .= '<br/>';
        if ($image->source && $image->subject_category !== 'Flora of the Otway Plain and Ranges Plate') {
            $caption .= "<b>Source:</b> {$image->source}<br/>";
        }
        $caption .= $image->subtype === 'Illustration' ? '<b>Illustration:</b> ' : '<b>Photo:</b> ';
        $caption .= $image->creator;

        if (strpos($license, 'CC BY') !== false) {
            $caption .= ", {$license}";
        }
        else {
            $caption .= ', &copy; ' . date('Y');
            $copyrightOwner = $image->copyright_owner;
            if ($copyrightOwner === 'Royal Botanic Gardens Victoria') {
                $copyrightOwner = 'Royal Botanic Gardens Board';
            }
            if ($copyrightOwner) {
                $caption .= " {$copyrightOwner}";
            }
            else {
                $caption .= " Royal Botanic Gardens Board";
            }
            if ($image->subject_category == 'Flora of the Otway Plain and Ranges Plate') {
                $caption .= ". {$image->rights}. Not to be reproduced without 
                prior permission from CSIRO Publishing.";
            }
            else {
                $caption .= ", {$license}";
            }
        }

        return $caption;
    }
}
