import Foundation
import AVFoundation
import CoreGraphics
import ImageIO

let fm = FileManager.default
let root = URL(fileURLWithPath: fm.currentDirectoryPath)
let frameDir = root.appendingPathComponent("video-test/frames")
let output = root.appendingPathComponent("video-test/renders/ipet-test.mov")
try? fm.removeItem(at: output)
let width = 1920, height = 1080, fps = 30
let writer = try AVAssetWriter(outputURL: output, fileType: .mov)
let settings: [String: Any] = [AVVideoCodecKey: AVVideoCodecType.proRes422, AVVideoWidthKey: width, AVVideoHeightKey: height]
let input = AVAssetWriterInput(mediaType: .video, outputSettings: settings)
input.expectsMediaDataInRealTime = false
writer.add(input)
let adaptor = AVAssetWriterInputPixelBufferAdaptor(assetWriterInput: input, sourcePixelBufferAttributes: [kCVPixelBufferPixelFormatTypeKey as String: Int(kCVPixelFormatType_32BGRA), kCVPixelBufferWidthKey as String: width, kCVPixelBufferHeightKey as String: height])
writer.startWriting()
writer.startSession(atSourceTime: .zero)

func load(_ url: URL) -> CGImage? {
    guard let source = CGImageSourceCreateWithURL(url as CFURL, nil) else { return nil }
    return CGImageSourceCreateImageAtIndex(source, 0, nil)
}

for i in 0..<150 {
    while !input.isReadyForMoreMediaData { Thread.sleep(forTimeInterval: 0.001) }
    let url = frameDir.appendingPathComponent(String(format: "frame-%04d.png", i))
    guard let cg = load(url) else { fatalError("Missing frame") }
    var buffer: CVPixelBuffer?
    CVPixelBufferCreate(kCFAllocatorDefault, width, height, kCVPixelFormatType_32BGRA, nil, &buffer)
    guard let buffer else { fatalError("Cannot allocate buffer") }
    CVPixelBufferLockBaseAddress(buffer, [])
    let ctx = CGContext(data: CVPixelBufferGetBaseAddress(buffer), width: width, height: height, bitsPerComponent: 8, bytesPerRow: CVPixelBufferGetBytesPerRow(buffer), space: CGColorSpaceCreateDeviceRGB(), bitmapInfo: CGImageAlphaInfo.premultipliedFirst.rawValue | CGBitmapInfo.byteOrder32Little.rawValue)
    ctx?.draw(cg, in: CGRect(x: 0, y: 0, width: width, height: height))
    CVPixelBufferUnlockBaseAddress(buffer, [])
    if !adaptor.append(buffer, withPresentationTime: CMTime(value: CMTimeValue(i), timescale: CMTimeScale(fps))) {
        print("append failed at \(i): \(writer.error?.localizedDescription ?? "unknown")")
        break
    }
}
input.markAsFinished()
let group = DispatchGroup()
group.enter()
writer.finishWriting { group.leave() }
group.wait()
print("status=\(writer.status.rawValue) error=\(writer.error?.localizedDescription ?? "none") output=\(output.path)")
