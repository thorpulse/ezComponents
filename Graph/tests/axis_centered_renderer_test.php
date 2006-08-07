<?php
/**
 * ezcGraphAxisCenteredRendererTest 
 * 
 * @package Graph
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tests for ezcGraph class.
 * 
 * @package ImageAnalysis
 * @subpackage Tests
 */
class ezcGraphAxisCenteredRendererTest extends ezcTestCase
{

    protected $renderer;

    protected $driver;

	public static function suite()
	{
		return new ezcTestSuite( "ezcGraphAxisCenteredRendererTest" );
	}

    /**
     * setUp 
     * 
     * @access public
     */
    public function setUp()
    {
    }

    /**
     * tearDown 
     * 
     * @access public
     */
    public function tearDown()
    {
    }

    public function testRenderAxisGrid()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawGridLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 220., 20. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 220., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 460., 20. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisOuterGrid()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->axisLabelRenderer->outerGrid = true;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawGridLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 220., 0. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 220., 200. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 460., 0. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 200. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisSteps()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawStepLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 220, 177. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 220, 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 460., 177. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisOuterSteps()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->axisLabelRenderer->outerStep = true;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawStepLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 220., 177. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 220., 183. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 460., 177. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 183. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisNoInnerSteps()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->axisLabelRenderer->innerStep = false;
        $chart->xAxis->axisLabelRenderer->outerStep = true;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawStepLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 220., 180. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 220., 183. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawStepLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 460., 180. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 183. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#EEEEEC' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisNoSteps()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->axisLabelRenderer->innerStep = false;
        $chart->yAxis->axisLabelRenderer->innerStep = false;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawStepLine',
        ) );

        $mockedRenderer
            ->expects( $this->exactly( 0 ) )
            ->method( 'drawStepLine' );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderTextBoxes()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 182., 182., 258., 198. ), 1. ),
                $this->equalTo( 'sample 2' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 422., 182., 498., 198. ), 1. ),
                $this->equalTo( 'sample 5' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderTextBoxesWithZeroValue()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->axisLabelRenderer->showZeroValue = true;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 182., 178., 198. ), 1. ),
                $this->equalTo( 'sample 1' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );
        $mockedRenderer
            ->expects( $this->at( 1 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 182., 182., 258., 198. ), 1. ),
                $this->equalTo( 'sample 2' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );
        $mockedRenderer
            ->expects( $this->at( 4 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 422., 182., 498., 198. ), 1. ),
                $this->equalTo( 'sample 5' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisGridFromRight()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->xAxis->position = ezcGraph::RIGHT;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawGridLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 380., 20. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 380., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 140., 20. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 140., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisGridFromTop()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->position = ezcGraph::TOP;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawGridLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 140., 60. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 60. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 140., 180. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 180. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderAxisGridFromBottom()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->position = ezcGraph::BOTTOM;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawGridLine',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 140., 140. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 140. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawGridLine' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 140., 20. ), 1. ),
                $this->equalTo( new ezcGraphCoordinate( 460., 20. ), 1. ),
                $this->equalTo( ezcGraphColor::fromHex( '#888A85' ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderTextBoxesFromRight()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->xAxis->position = ezcGraph::RIGHT;
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 342., 182., 418., 198. ), 1. ),
                $this->equalTo( 'sample 2' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 182., 178., 198. ), 1. ),
                $this->equalTo( 'sample 5' ),
                $this->equalTo( ezcGraph::TOP | ezcGraph::CENTER )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderTextBoxesFromTop()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->position = ezcGraph::TOP;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 42., 138., 78. ), 1. ),
                $this->equalTo( '100' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 162., 138., 198. ), 1. ),
                $this->equalTo( '400' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testRenderTextBoxesFromBottom()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisCenteredLabelRenderer();
        $chart->yAxis->position = ezcGraph::BOTTOM;
        $chart['sampleData'] = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 122., 138., 158. ), 1. ),
                $this->equalTo( '100' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT )
            );
        $mockedRenderer
            ->expects( $this->at( 3 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 102., 2., 138., 38. ), 1. ),
                $this->equalTo( '400' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }
}
?>
