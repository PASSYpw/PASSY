<?php

require_once __DIR__ . "/../config.inc.php";
?>
<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 569.2 952.76" width="50px" height="50px">
                    <path d="M500,41.24c-157.12,0-284.49,127.37-284.49,284.49h0V692.16a35.28,35.28,0,0,0,35.28,35.28h0A35.28,35.28,0,0,0,286,692.16V513.25l.57.66A283.8,283.8,0,0,0,500,610.23c157.12,0,284.49-127.37,284.49-284.49S657.11,41.24,500,41.24Zm0,497.86c-117.84,0-213.37-95.53-213.37-213.37S382.15,112.37,500,112.37s213.37,95.53,213.37,213.37S617.83,539.1,500,539.1Z"
                          transform="translate(-215.5 -41.24)"></path>
                    <text transform="translate(0 901.39)"
                          style="font-size:190px; font-family:Roboto-Regular, Roboto, serif; letter-spacing:-0.07em">
                        <tspan>PASSY</tspan>
                    </text>
                </svg>
                <span class="text-muted hidden-xs"><?php echo $config["passy"]["version"] ?></span>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <p class="text-muted">
                    Store your passwords securely.
                </p>
            </div>
            <div class="col-sm-6 col-xs-12 text-right">
                <ul class="list-inline">
                    <li><a href="<?php echo $config["passy"]["github"] ?>" target="_blank">GitHub</a></li>
                    <li><a href="<?php echo $config["passy"]["issues"] ?>" target="_blank">Bug report</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
