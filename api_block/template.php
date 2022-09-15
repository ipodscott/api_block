<?php
/**
 * Block Name: API Block
 *
 * This is the template for an API Block
 */

// Create id attribute allowing for custom "anchor" value.
$id = $block['id'];
if (!empty($block['anchor']))
{
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = '';
if (!empty($block['className']))
{
    $className .= ' ' . $block['className'];
}
if (!empty($block['align']))
{
    $className .= ' align' . $block['align'];
}

/* Begin API functions */

// create curl resource
$ch = curl_init();
// set url
curl_setopt($ch, CURLOPT_URL, "https://dev-wildfire-assessment.pantheonsite.io/wp-json/wildfire/v1/locations/");
//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$headers = ['Authorization: Basic d2lsZGZpcmU6MTAyOTM4NDc1Njg0ODM5Mw==', ];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// $output contains the output string
$output = curl_exec($ch);

// close curl resource to free up system resources
curl_close($ch);

$response = json_decode($output);

if ($response->Data)
{
    $all_locations = $response->Data;
    
    $unique_states = array();
   
    foreach ($response->Data as $item)
    {
        $unique_states[$item->state] = strtolower(str_replace(' ', '_', $item->state));
    } 
    ksort($unique_states);
}
?>

<?php if ($all_locations): ?>

<div <?php echo esc_attr($id); ?> class="api-block <?php echo esc_attr($className); ?>">

  <div class="state-filter-containter">
        <form class="state-filter-dropdown">
          <select class="location-select">
              <option value="all">Filter by State</option>
              <?php foreach ($unique_states as $state_name => $state_slug): ?>
              <option value="<?php echo $state_slug ?>"><?php echo $state_name ?></option>
              <?php endforeach; ?>
          </select>
        </form>
    </div>

  <div class="location-card-container ">

    <?php foreach ($all_locations as $item): ?>
    <?php
        $state_slug = $item->state;
        $state_slug = strtolower(str_replace(' ', '_', $item->state));
        $short_des = $item->description;
        $short_des = wordwrap($short_des, 73);
        $short_des = explode("\n", $short_des);
        $short_des = $short_des[0] . '... Read More ';
    ?>
        <a href="<?php echo $item->url; ?>" data-eventtype="<?php echo $state_slug;?>" class="location-card" id="location_<?php echo $item->id ?>" aria-label="Read more about <?php echo $item->state ; ?>'s <?php echo $item->description; ?>">
            <div class="location-title"><?php echo $item->state ; ?></div>
            <div role="img" aria-label="<?php echo $item->state ; ?> - <?php echo $item->description;?>" class="location-image" style="background-image:url(<?php echo $item->image_url ?>)">  </div>
            <div class="location-description"><?php echo $short_des ; ?></div>
            <div class="location-address">
                <?php echo $item->street_address; ?><br />
                <?php echo $item->city; ?>, <?php echo $item->state; ?><br />
                <?php echo $item->zip; ?>
            </div>
            <h2></h2>                     
        </a>
    <?php endforeach; ?>
    
  </div>

</div>

  <?php else: ?>
  <?php endif; ?>
  <?php //print "<pre>" . print_r($response->Data, 1) . "</pre>"; ?>