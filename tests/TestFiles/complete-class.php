<?php
namespace Shared\Charts;

use Shared\Databases\Interfaces\DatasetInterface;
use Shared\DateControl\Date;
use Shared\Http\HttpHeaders;

/**
 * (no summary)
 */
class Chart
{
    /**************************
     *   Instance variables   *
     *************************/

    /**
     * @var  Settings
     */
    public $settings;

    /**
     * @var  bool
     */
    private $dimensionsHaveBeenCalculated;

    /**
     * @var  bool
     */
    private $imageHasBeenCreated;

    /**
     * @var  bool
     */
    private $imageHasBeenInitialized;

    /**
     * @var  array
     */
    private $lines = [];


    /************************
     *   Instance methods   *
     ***********************/

    public function __construct()
    {
        $this->settings = new Settings();
    }

    public function addArray(array $array, string $name = null, int $rowFormat = null): void
    {
        $this->settings->getData()->addArray($array, $name, $rowFormat);
    }

    /**
     * @param  int|string|null  $name
     *
     * @return  Interfaces\DatasetInterface
     */
    public function getDataset($name = null): ?Interfaces\DatasetInterface
    {
        return $this->settings->getData()->getDataset($name);
    }

    public function addDatastoreDataset(DatasetInterface $data, string $name = null): void
    {
        $this->addArray($data->toArray(), $name);
    }

    public function getError(int $index = null): ?string
    {
        $errorCount = count($this->settings->getErrors());

        if ($errorCount === 0) {
            return null;
        }

        if (is_numeric($index) && isset($this->errors[$errorCount - 1 - $index])) {
            return $this->settings->getErrors()[$errorCount - 1 - $index];
        }

        if ($index === null) {
            return $this->settings->getErrors()[$errorCount - 1];
        }

        return null;
    }

    public function addFormula(string $formula, float $from, float $to, string $name = null): void
    {
        $this->settings->getData()->addFormula($formula, $from, $to, $name);
    }

    public function addHorizontalLine(float $y, Color $color): void
    {
        $this->lines[] = [null, $y, null, $y, $color];
    }

    public function addLine(float $x1, float $y1, float $x2, float $y2, Color $color): void
    {
        $this->lines[] = [$x1, $y1, $x2, $y2, $color];
    }

    public function addMovingAverage(Interfaces\DatasetInterface $sourceSet, int $span, bool $addTails = true, string $name = null): void
    {
        $this->settings->getData()->addMovingAverage($sourceSet, $span, $addTails, $name);
    }

    public function addMovingAverage2(Interfaces\DatasetInterface $sourceSet, float $width, string $name = null): void
    {
        $this->settings->getData()->addMovingAverage2($sourceSet, $width, $name);
    }

    public function toBrowser(): void
    {
        HttpHeaders::checkIfSendable();

        $this->createImage();
        $this->settings->getIm()->toBrowser();
    }

    public function toFile(string $filename = null): string
    {
        $this->createImage();

        if (empty($filename)) {
            $filename = $this->settings->getFileDir() . uniqid() . '.png';
        }

        $this->settings->getIm()->toFile($filename);

        return $filename;
    }

    public function toString(): string
    {
        $this->createImage();

        return $this->settings->getIm()->toString();
    }

    public function addVerticalLine(float $x, Color $color): void
    {
        $this->lines[] = [$x, null, $x, null, $color];
    }

    private function calculateDimensions(): void
    {
        if ($this->dimensionsHaveBeenCalculated !== null) {
            return;
        }

        $this->dimensionsHaveBeenCalculated = false;

        $this->settings->getXAxis()->autofitRange(
            $this->settings->getData()->getMinX(),
            $this->settings->getData()->getMaxX()
        );
        $this->settings->getYAxis()->autofitRange(
            $this->settings->getData()->getMinY(),
            $this->settings->getData()->getMaxY()
        );
        $this->settings->getY2Axis()->autofitRange(
            $this->settings->getData()->getMinY2(),
            $this->settings->getData()->getMaxY2()
        );
        $this->calculateXDimensions();
        $this->calculateYDimensions();

        // The dimenions have been calculated
        $this->dimensionsHaveBeenCalculated = true;
    }

    private function calculateXDimensions(): void
    {
        /*
         * The width consists of:
         *  - Left padding
         *  - Y axis width
         *  - Chart width
         *  - Secondary Y axis with if applicable
         *  - Legend width if it's positioned in the right margin
         *  - Left padding
         */
        $leftPadding  = $this->settings->getImageLeftPadding();
        $yAxisWidth   = $this->settings->getYAxis()->getWidth();
        $y2AxisWidth  = $this->settings->getY2Axis()->getWidth();
        $legendWidth  = $this->settings->getLegend()->getWidthInRightMargin();
        $rightPadding = $this->settings->getImageRightPadding();

        if ($this->settings->getSizeLock() === Settings::IMAGE_SIZE) {
            $imageWidth = $this->settings->getImageWidth();

            $chartWidth = $imageWidth
                - $leftPadding
                - $yAxisWidth
                - $y2AxisWidth
                - $legendWidth
                - $rightPadding;

            if ($chartWidth < 1) {
                throw new Exceptions\ImageNotWideEnoughException(-$chartWidth);
            }

            $this->settings->setChartWidth($chartWidth);
        }
        else {
            $chartWidth = $this->settings->getChartWidth();

            $imageWidth = $leftPadding
                + $yAxisWidth
                + $y2AxisWidth
                + $chartWidth
                + $legendWidth
                + $rightPadding;

            $this->settings->setImageWidth($imageWidth);
        }

        $this->settings->setXo($leftPadding + $yAxisWidth);
        $this->settings->setXm($leftPadding + $yAxisWidth + $chartWidth);
    }

    private function calculateYDimensions(): void
    {
        /*
         * The height consists of:
         *  - Top padding
         *  - Chart title height
         *  - Chart height
         *  - X axis height
         *  - Bottom padding
         */
        $topPadding       = $this->settings->getImageTopPadding();
        $chartTitleHeight = $this->getChartTitleHeight();
        $xAxisHeight      = $this->settings->getXAxis()->getHeight();
        $bottomPadding    = $this->settings->getImageBottomPadding();

        if ($this->settings->getSizeLock() === Settings::IMAGE_SIZE) {
            $imageHeight = $this->settings->getImageHeight();
            $chartHeight = $imageHeight - $topPadding - $chartTitleHeight - $xAxisHeight - $bottomPadding;

            if ($chartHeight < 1) {
                throw new Exceptions\ImageNotHighEnoughException(-$chartHeight);
            }

            $this->settings->setChartHeight($chartHeight);
        }
        else {
            $chartHeight = $this->settings->getChartHeight();
            $imageHeight = $topPadding + $chartTitleHeight + $chartHeight + $xAxisHeight + $bottomPadding;

            $this->settings->setImageHeight($imageHeight);
        }

        $this->settings->setYo($topPadding + $chartTitleHeight + $chartHeight);
        $this->settings->setYm($topPadding + $chartTitleHeight);
    }

    private function getChartTitleHeight(): float
    {
        if (!$this->settings->getShowChartTitle() || !$this->settings->getChartTitle()) {
            return 0.0;
        }

        $height = Functions::getTextHeight(
            $this->settings->getChartTitle(),
            $this->settings->getChartTitleFont(),
            $this->settings->getChartTitleSize()
        );

        return $height + $this->settings->getChartTitleMargin();
    }

    private function checkValueForWeekendDayBackground(float $subvalue, Color $fillColor): void
    {
        if (!in_array(date('w', $subvalue), [0, 6])) {
            return;
        }

        $xAxis = $this->settings->getXAxis();

        // The left side of the block should be the start of this day
        $x  = $xAxis->valueToCoordinate(Date::fromTimestamp($subvalue)->getTimestamp());
        $xm = $this->settings->getXm();

        if (Functions::isMoreThanOrEqual($x, $xm)) {
            $x = $xm;
        }

        // The right side of the block should be the start of the next day
        $nextX = $xAxis->valueToCoordinate(Date::fromTimestamp($subvalue)->getNext()->getTimestamp());

        $this->settings->getIm()->drawRectangle(
            max($x, $this->settings->getXo()),
            $this->settings->getYo(),
            min($nextX, $xm),
            $this->settings->getYm(),
            $fillColor,
            $fillColor
        );
    }

    private function drawAxes(): void
    {
        $this->settings->getXAxis()->drawYourLine();
        $this->settings->getYAxis()->drawYourLine();
        $this->settings->getY2Axis()->drawYourLine();
        $this->settings->getXAxis()->drawYourLabels();
        $this->settings->getYAxis()->drawYourLabels();
        $this->settings->getY2Axis()->drawYourLabels();
    }

    private function drawData(): void
    {
        $this->settings->getData()->drawData();

        // Verwijderen wat "buiten de lijntjes" van het grid getekend is, met een kleine marge (data_grid_overflow)
        $image      = $this->settings->getIm();
        $dataGridOverflow = $this->settings->getDataGridOverflow();
        $bgcolor    = $this->settings->getBackgroundColor();
        $xo         = $this->settings->getXo();
        $xm         = $this->settings->getXm();
        $ym         = $this->settings->getYm();
        $yo         = $this->settings->getYo();
        $imageWidth = $this->settings->getImageWidth();
        $imageHeight = $this->settings->getImageHeight();

        $image->drawRectangle(0, 0, $imageWidth, $ym - $dataGridOverflow, null, $bgcolor);
        $image->drawRectangle(
            0,
            $ym - $dataGridOverflow,
            $xo - $dataGridOverflow,
            $yo + $dataGridOverflow,
            null,
            $bgcolor
        );
        $image->drawRectangle(0, $yo + $dataGridOverflow, $imageWidth, $imageHeight, null, $bgcolor);
        $image->drawRectangle(
            $xm + $dataGridOverflow,
            $ym - $dataGridOverflow,
            $imageWidth,
            $yo + $dataGridOverflow,
            null,
            $bgcolor
        );
    }

    private function drawGrid(): void
    {
        // Draw x axis subgrid lines
        $subgridColor = $this->settings->getSubgridColor();
        $gridColor = $this->settings->getGridColor();
        $image     = $this->settings->getIm();
        $xAxis     = $this->settings->getXAxis();
        $yAxis     = $this->settings->getYAxis();
        $xo        = $this->settings->getXo();
        $xm        = $this->settings->getXm();
        $yo        = $this->settings->getYo();
        $ym        = $this->settings->getYm();

        if ($xAxis->shouldFillWeekendDays()) {
            $this->fillWeekendDaysBackground();
        }

        // Draw whispy lines
        $image->setLineThickness(.5);

        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($xAxis as $value) {
            $xAxis->rewindSubloop();

            while (true) {
                $subvalue = $xAxis->nextSubloop();

                if ($subvalue === null) {
                    break;
                }

                $x = $xAxis->valueToCoordinate($subvalue);

                if (Functions::isMoreThanOrEqual($x, $xm)) {
                    break;
                }

                $image->drawLine($x, $yo, $x, $ym, $subgridColor);
            }
        }

        // Draw y axis subgrid lines
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($yAxis as $value) {
            $yAxis->rewindSubloop();

            while (true) {
                $subvalue = $yAxis->nextSubloop();

                if ($subvalue === null) {
                    break;
                }

                $y = $yAxis->valueToCoordinate($subvalue);

                if (Functions::isLessThanOrEqual($y, $ym)) {
                    break;
                }

                $image->drawLine($xo, $y, $xm, $y, $subgridColor);
            }
        }

        // Draw x axis primary grid lines
        foreach ($xAxis as $value) {
            $x = $xAxis->valueToCoordinate($value);

            $image->drawLine($x, $yo, $x, $ym, $gridColor);
        }

        // Draw y axis primary grid lines
        foreach ($yAxis as $value) {
            $y = $yAxis->valueToCoordinate($value);

            $image->drawLine($xo, $y, $xm, $y, $gridColor);
        }

        // Draw manually added lines
        foreach ($this->lines as $line) {
            [$x1, $y1, $x2, $y2, $color] = $line;

            $x1 = ($x1 === null) ? $xo : $xAxis->valueToCoordinate($x1);
            $y1 = ($y1 === null) ? $yo : $yAxis->valueToCoordinate($y1);
            $x2 = ($x2 === null) ? $xm : $xAxis->valueToCoordinate($x2);
            $y2 = ($y2 === null) ? $ym : $yAxis->valueToCoordinate($y2);

            $image->drawLine($x1, $y1, $x2, $y2, $color);
        }

        // Restore line thickness
        $image->setLineThickness(1);
    }

    private function drawLegend(): void
    {
        if (!$this->settings->getShowLegend()) {
            return;
        }

        $this->settings->getLegend()->drawYourself();
    }

    private function drawTitles(): void
    {
        if (!$this->settings->getShowChartTitle() || !$this->settings->getChartTitle()) {
            return;
        }

        $this->settings->getIm()->drawText(
            $this->settings->getChartTitle(),
            ($this->settings->getXo() + $this->settings->getXm()) / 2,
            $this->settings->getYm() - $this->settings->getChartTitleMargin(),
            $this->settings->getChartTitleSize(),
            $this->settings->getChartTitleColor(),
            $this->settings->getChartTitleFont(),
            Image::CENTER,
            0
        );
    }

    private function drawYourself(): void
    {
        $image = $this->settings->getIm();

        // Draw the grid
        $this->drawGrid();
        $this->drawData();

        // Undo our magic for textual elements
        $image->undoPseudofactor();
        $image->useAlphaBlending();

        // Draw textual elements
        $this->drawLegend();
        $this->drawAxes();
        $this->drawTitles();
    }

    private function fillWeekendDaysBackground(): void
    {
        $fillColor = Color::mix(
            $this->settings->getSubgridColor(),
            $this->settings->getBackgroundColor(),
            .8
        );

        $xAxis = $this->settings->getXAxis();

        foreach ($xAxis as $value) {
            $this->checkValueForWeekendDayBackground($value, $fillColor);
            $xAxis->rewindSubloop();

            while (true) {
                $subvalue = $xAxis->nextSubloop();

                if ($subvalue === null) {
                    break;
                }

                $this->checkValueForWeekendDayBackground($subvalue, $fillColor);
            }
        }
    }

    private function createImage(): void
    {
        if ($this->imageHasBeenCreated !== null) {
            return;
        }

        $this->imageHasBeenCreated = false;

        $this->calculateDimensions();
        $this->initializeImage();
        $this->drawYourself();

        // The image has been created
        $this->imageHasBeenCreated = true;
    }

    private function initializeImage(): void
    {
        if ($this->imageHasBeenInitialized !== null) {
            return;
        }

        $this->imageHasBeenInitialized = false;

        $this->calculateDimensions();

        $image = new Image(
            $this->settings->getImageWidth(),
            $this->settings->getImageHeight(),
            $this->settings->getPseudoAntialiasing()
        );

        $this->settings->setIm($image);

        if (Functions::areEqual($this->settings->getPseudoAntialiasing(), 1)) {
            $image->setAntialias(true);
        }

        $image->useTransparency($this->settings->getTransparentBackground());
        $image->setBackground($this->settings->getBackgroundColor());

        $legendBackgroundColor = $this->settings->getBackgroundColor();

        $legendBackgroundColor->setOpacity($this->settings->getLegendBackgroundOpacity() / 100);
        $this->settings->setLegendBackgroundColor($legendBackgroundColor);

        // The image has been initialized
        $this->imageHasBeenInitialized = true;
    }
}
